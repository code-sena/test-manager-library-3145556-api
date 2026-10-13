<?php

namespace App\Adapters\Outbound\Persistence;

use App\Application\Ports\MemberRepository;
use App\Domain\Member as DomainMember;
use Illuminate\Support\Facades\DB;

class MemberRepositoryImpl implements MemberRepository
{
    public function findById(string $id): ?DomainMember
    {
        $row = DB::table('library.member')->where('id', $id)->first();

        if (! $row) {
            return null;
        }

        return $this->toDomain($row);
    }

    public function findByDocument(string $document): ?DomainMember
    {
        $row = DB::table('library.member')->where('document_number', $document)->first();

        if (! $row) {
            return null;
        }

        return $this->toDomain($row);
    }

    public function search(?string $q, int $page, int $size): array
    {
        $query = DB::table('library.member');

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('full_name', 'ILIKE', "%{$q}%")
                  ->orWhere('document_number', 'ILIKE', "%{$q}%");
            });
        }

        $query->orderBy('full_name');

        $rows = $query->forPage($page, $size)->get();

        return array_map(fn($r) => $this->toDomain($r), $rows->all());
    }

    public function countSearch(?string $q): int
    {
        $query = DB::table('library.member');

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('full_name', 'ILIKE', "%{$q}%")
                  ->orWhere('document_number', 'ILIKE', "%{$q}%");
            });
        }

        return $query->count();
    }

    public function save(DomainMember $member): void
    {
        DB::table('library.member')->updateOrInsert(
            ['id' => $member->id],
            [
                'full_name' => $member->fullName,
                'document_number' => $member->documentNumber->value,
            ]
        );
    }

    private function toDomain(object $row): DomainMember
    {
        return new DomainMember(
            id: $row->id,
            fullName: $row->full_name,
            documentNumber: new \App\Domain\ValueObjects\DocumentNumber($row->document_number),
        );
    }
}
