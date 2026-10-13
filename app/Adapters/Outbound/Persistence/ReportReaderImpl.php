<?php

namespace App\Adapters\Outbound\Persistence;

use App\Application\Ports\ReportReader;
use Illuminate\Support\Facades\DB;

class ReportReaderImpl implements ReportReader
{
    public function mostBorrowed(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $startDate = $from->format('Y-m-d 00:00:00+00');
        $endDate = $to->modify('+1 day')->format('Y-m-d 00:00:00+00');

        $rows = DB::table('library.loan_item')
            ->join('library.loan', 'library.loan.id', '=', 'library.loan_item.loan_id')
            ->where('library.loan.loaned_at', '>=', $startDate)
            ->where('library.loan.loaned_at', '<', $endDate)
            ->groupBy('library.loan_item.book_id', 'library.loan_item.book_title', 'library.loan_item.category_name')
            ->orderByDesc('times_borrowed')
            ->orderBy('library.loan_item.book_title')
            ->get([
                'library.loan_item.book_id',
                'library.loan_item.book_title',
                'library.loan_item.category_name',
                DB::raw('COUNT(*) as times_borrowed'),
            ]);

        return array_map(fn($row) => [
            'book_id' => $row->book_id,
            'book_title' => $row->book_title,
            'category_name' => $row->category_name,
            'times_borrowed' => (int) $row->times_borrowed,
        ], $rows->all());
    }
}
