<?php

namespace App;

use App\Models\BloodGroup;
use App\Models\Lga;
use App\Models\Nationality;
use App\Models\StaffRecord;
use App\Models\State;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\School;
use App\Models\Branch;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'phone', 'phone2', 'dob', 'gender', 'photo', 'address', 'bg_id', 'password', 'nal_id', 'state_id', 'lga_id', 'code', 'user_type', 'email_verified_at', 'school_id', 'branch_id', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function student_record()
    {
        return $this->hasOne(StudentRecord::class);
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nal_id');
    }

    public function blood_group()
    {
        return $this->belongsTo(BloodGroup::class, 'bg_id');
    }

    public function staff()
    {
        return $this->hasMany(StaffRecord::class);
    }
        /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Scopes
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    // Helper methods
    public function isSuperAdmin()
    {
        return $this->role && $this->role->level <= Role::SUPER_ADMIN;
    }

    public function isSchoolAdmin()
    {
        return $this->role && $this->role->level <= Role::SCHOOL_ADMIN;
    }

    public function isBranchAdmin()
    {
        return $this->role && $this->role->level <= Role::BRANCH_ADMIN;
    }

    public function isStaff()
    {
        return $this->role && $this->role->level === Role::STAFF;
    }

    public function canAccessSchool($schoolId)
    {
        if ($this->isSuperAdmin()) return true;
        return $this->school_id == $schoolId;
    }

    public function canAccessBranch($branchId)
    {
        if ($this->isSuperAdmin()) return true;
        if ($this->isSchoolAdmin()) {
            $branch = Branch::find($branchId);
            return $branch && $branch->school_id == $this->school_id;
        }
        return $this->branch_id == $branchId;
    }

    public function getCurrentBranchId()
    {
        if ($this->isSuperAdmin()) {
            return session('selected_branch_id', $this->branch_id);
        } elseif ($this->isSchoolAdmin()) {
            return session('selected_branch_id', $this->branch_id);
        }
        return $this->branch_id;
    }
}