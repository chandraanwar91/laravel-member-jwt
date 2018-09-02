<?php
namespace Tests\Unit\Member;

use Tests\TestCase;

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\MemberProfile;
use App\Models\Member;
use App\Repositories\MemberRepository;
use App\Repositories\MemberProfileRepository;

class MemberTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_member_can_be_created()
    {
        $memberData = factory(Member::class)->make();
        $memberDataArr = $memberData->toArray();

        $memberRepo = new MemberRepository(new Member,new MemberProfile);

        $member = $memberRepo->create($memberDataArr);

        $this->assertInstanceOf(Member::class, $member);
        $this->assertEquals($memberDataArr['fullname'], $member->fullname);
        $this->assertEquals($memberDataArr['email'], $member->email);
        $this->assertEquals($memberDataArr['status'], $member->status);
        $this->assertEquals($memberDataArr['source'], $member->source);

    }

     /** @test */
    public function a_member_can_be_updated()
    {
        $member = factory(Member::class)->create();
        $memberUpdateData['fullname'] = 'testUpdate';
        $memberUpdateData['phone_number'] = '999999999';
        $memberUpdateData['status'] = 'suspend';

        $memberRepo = new MemberRepository(new Member,new MemberProfile);

        $memberUpdated = $memberRepo->update($memberUpdateData,$member->id);

        $memberProfileUpdated = $memberUpdated->profile;

        $this->assertInstanceOf(Member::class, $memberUpdated);
        $this->assertEquals($memberUpdateData['fullname'], $memberUpdated->fullname);
        $this->assertEquals($memberUpdateData['status'], $memberUpdated->status);
        $this->assertEquals($memberUpdateData['phone_number'], $memberProfileUpdated->phone_number);
    }


     /** @test */
    public function a_member_can_be_showed()
    {
        $member = factory(Member::class)->create();

        $memberRepo = new MemberRepository(new Member,new MemberProfile);

        $memberFound = $memberRepo->findById($member->id);

        $this->assertInstanceOf(Member::class, $memberFound);
        $this->assertEquals($member->fullname, $memberFound->fullname);
        $this->assertEquals($member->email, $memberFound->email);
    }


    /** @test */
    public function a_member_can_be_deleted()
    {
        $member = factory(Member::class)->create();
      
        $memberRepo = new MemberRepository(new Member,new MemberProfile);

        $memberDeleted = $memberRepo->delete($member->id);
        
        $this->assertTrue($memberDeleted);
    }

}
