<?php

namespace App\Adapters\Outbound\Persistence;

use App\Application\Ports\LoanRepository;
use App\Domain\Loan as DomainLoan;
use App\Domain\LoanItem as DomainLoanItem;
use App\Domain\LoanStatus;
use Illuminate\Support\Facades\DB;

class LoanRepositoryImpl implements LoanRepository
{
    public function findById(string $id): ?DomainLoan
    {
        $loanRow = DB::table('library.loan')->where('id', $id)->first();

        if (! $loanRow) {
            return null;
        }

        $itemRows = DB::table('library.loan_item')->where('loan_id', $id)->get();

        return $this->toDomain($loanRow, $itemRows);
    }

    public function save(DomainLoan $loan): void
    {
        DB::transaction(function () use ($loan) {
            DB::table('library.loan')->insert([
                'id' => $loan->id,
                'member_id' => $loan->memberId,
                'loaned_by_user_id' => $loan->loanedByUserId,
                'loaned_at' => $loan->loanedAt->format('Y-m-d H:i:s.uP'),
            ]);

            foreach ($loan->items as $item) {
                DB::table('library.loan_item')->insert([
                    'id' => $item->id,
                    'loan_id' => $item->loanId,
                    'book_id' => $item->bookId,
                    'book_title' => $item->bookTitle,
                    'isbn' => $item->isbn,
                    'category_name' => $item->categoryName,
                    'due_at' => $item->dueAt->format('Y-m-d H:i:s.uP'),
                    'returned_at' => $item->returnedAt?->format('Y-m-d H:i:s.uP'),
                ]);
            }
        });
    }

    public function list(?\DateTimeImmutable $from, ?\DateTimeImmutable $to, ?string $status, ?string $memberId, int $page, int $size): array
    {
        $query = DB::table('library.loan')->select('library.loan.*');

        if ($from) {
            $query->where('loaned_at', '>=', $from->format('Y-m-d H:i:s.uP'));
        }

        if ($to) {
            $query->where('loaned_at', '<', $to->modify('+1 day')->format('Y-m-d H:i:s.uP'));
        }

        if ($memberId) {
            $query->where('member_id', $memberId);
        }

        if ($status) {
            $query->whereRaw($this->statusSqlCondition($status));
        }

        $query->orderBy('loaned_at', 'desc');

        $rows = $query->forPage($page, $size)->get();

        $result = [];
        foreach ($rows as $row) {
            $itemRows = DB::table('library.loan_item')->where('loan_id', $row->id)->get();
            $result[] = $this->toDomain($row, $itemRows);
        }

        return $result;
    }

    public function countList(?\DateTimeImmutable $from, ?\DateTimeImmutable $to, ?string $status, ?string $memberId): int
    {
        $query = DB::table('library.loan');

        if ($from) {
            $query->where('loaned_at', '>=', $from->format('Y-m-d H:i:s.uP'));
        }

        if ($to) {
            $query->where('loaned_at', '<', $to->modify('+1 day')->format('Y-m-d H:i:s.uP'));
        }

        if ($memberId) {
            $query->where('member_id', $memberId);
        }

        if ($status) {
            $query->whereRaw($this->statusSqlCondition($status));
        }

        return $query->count();
    }

    public function findOverdueItems(): array
    {
        $now = now()->format('Y-m-d H:i:s.uP');

        $rows = DB::table('library.loan_item')
            ->join('library.loan', 'library.loan.id', '=', 'library.loan_item.loan_id')
            ->join('library.member', 'library.member.id', '=', 'library.loan.member_id')
            ->whereNull('library.loan_item.returned_at')
            ->where('library.loan_item.due_at', '<', $now)
            ->orderBy('library.loan_item.due_at')
            ->get([
                'library.loan.id as loan_id',
                'library.loan_item.id as item_id',
                'library.loan_item.book_title',
                'library.member.full_name as member_name',
                'library.loan_item.due_at',
            ]);

        return array_map(function ($row) use ($now) {
            $dueAt = new \DateTimeImmutable($row->due_at);
            $nowDt = new \DateTimeImmutable($now);

            return [
                'loan_id' => $row->loan_id,
                'item_id' => $row->item_id,
                'book_title' => $row->book_title,
                'member_name' => $row->member_name,
                'due_at' => $dueAt->format('Y-m-d\TH:i:s\Z'),
                'days_late' => (int) $nowDt->diff($dueAt)->format('%r%a'),
            ];
        }, $rows->all());
    }

    public function countActiveLoansForMember(string $memberId): int
    {
        return DB::table('library.loan_item')
            ->join('library.loan', 'library.loan.id', '=', 'library.loan_item.loan_id')
            ->where('library.loan.member_id', $memberId)
            ->whereNull('library.loan_item.returned_at')
            ->count();
    }

    public function hasOverdueLoansForMember(string $memberId): bool
    {
        $now = now()->format('Y-m-d H:i:s.uP');

        return DB::table('library.loan_item')
            ->join('library.loan', 'library.loan.id', '=', 'library.loan_item.loan_id')
            ->where('library.loan.member_id', $memberId)
            ->whereNull('library.loan_item.returned_at')
            ->where('library.loan_item.due_at', '<', $now)
            ->exists();
    }

    public function returnItem(string $loanId, string $itemId): DomainLoanItem
    {
        return DB::transaction(function () use ($loanId, $itemId) {
            $now = now()->format('Y-m-d H:i:s.uP');

            $affected = DB::table('library.loan_item')
                ->where('id', $itemId)
                ->where('loan_id', $loanId)
                ->whereNull('returned_at')
                ->update(['returned_at' => $now]);

            if ($affected === 0) {
                throw new \App\Domain\Exceptions\ItemAlreadyReturned();
            }

            $row = DB::table('library.loan_item')->where('id', $itemId)->first();

            return new DomainLoanItem(
                id: $row->id,
                loanId: $row->loan_id,
                bookId: $row->book_id,
                bookTitle: $row->book_title,
                isbn: $row->isbn,
                categoryName: $row->category_name,
                dueAt: new \DateTimeImmutable($row->due_at),
                returnedAt: new \DateTimeImmutable($row->returned_at),
            );
        });
    }

    private function statusSqlCondition(string $status): string
    {
        return match ($status) {
            'open' => 'EXISTS (SELECT 1 FROM library.loan_item li WHERE li.loan_id = library.loan.id AND li.returned_at IS NULL) AND NOT EXISTS (SELECT 1 FROM library.loan_item li WHERE li.loan_id = library.loan.id AND li.returned_at IS NULL AND li.due_at < NOW())',
            'overdue' => 'EXISTS (SELECT 1 FROM library.loan_item li WHERE li.loan_id = library.loan.id AND li.returned_at IS NULL AND li.due_at < NOW())',
            'closed' => 'NOT EXISTS (SELECT 1 FROM library.loan_item li WHERE li.loan_id = library.loan.id AND li.returned_at IS NULL)',
            default => 'TRUE',
        };
    }

    private function toDomain(object $loanRow, $itemRows): DomainLoan
    {
        $loan = new DomainLoan(
            id: $loanRow->id,
            memberId: $loanRow->member_id,
            loanedByUserId: $loanRow->loaned_by_user_id,
            loanedAt: new \DateTimeImmutable($loanRow->loaned_at),
        );

        foreach ($itemRows as $itemRow) {
            $item = new DomainLoanItem(
                id: $itemRow->id,
                loanId: $itemRow->loan_id,
                bookId: $itemRow->book_id,
                bookTitle: $itemRow->book_title,
                isbn: $itemRow->isbn,
                categoryName: $itemRow->category_name,
                dueAt: new \DateTimeImmutable($itemRow->due_at),
                returnedAt: $itemRow->returned_at ? new \DateTimeImmutable($itemRow->returned_at) : null,
            );

            $loan->items[] = $item;
        }

        return $loan;
    }
}
