<?php

namespace App\Application\Services;

use App\Application\Ports\MemberRepository;
use App\Domain\Exceptions\DocumentTaken;
use App\Domain\Member;
use App\Domain\ValueObjects\DocumentNumber;
use Illuminate\Support\Str;

class CreateMemberService
{
    public function __construct(
        private readonly MemberRepository $memberRepository,
    ) {
    }

    public function create(string $fullName, string $documentNumber): Member
    {
        $existing = $this->memberRepository->findByDocument($documentNumber);
        if ($existing !== null) {
            throw new DocumentTaken();
        }

        $member = new Member(
            id: Str::uuid()->toString(),
            fullName: trim($fullName),
            documentNumber: new DocumentNumber($documentNumber),
        );

        $this->memberRepository->save($member);

        return $member;
    }
}
