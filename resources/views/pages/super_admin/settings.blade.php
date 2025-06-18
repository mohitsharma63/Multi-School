
@extends('layouts.master')
@section('page_title', 'Schools Management')
@section('content')

<style>
    .schools-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 40px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .schools-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>') repeat;
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% { transform: translateX(0) translateY(0); }
        100% { transform: translateX(-50px) translateY(-50px); }
    }

    .management-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    .management-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .management-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #4CAF50, #45a049);
    }

    .management-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .card-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4CAF50, #45a049);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        color: white;
        font-size: 2rem;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }

    .card-description {
        color: #666;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .action-btn {
        background: linear-gradient(135deg, #4CAF50, #45a049);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .action-btn:hover {
        background: linear-gradient(135deg, #45a049, #4CAF50);
        transform: scale(1.05);
        color: white;
        text-decoration: none;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #4CAF50;
        margin-bottom: 10px;
    }

    .stat-label {
        color: #666;
        font-weight: 500;
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }

    .features-list li {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        position: relative;
        padding-left: 30px;
    }

    .features-list li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #4CAF50;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .quick-actions {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-top: 30px;
    }

    .quick-actions h5 {
        color: #333;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .quick-btn {
        background: white;
        border: 2px solid #e9ecef;
        padding: 15px 20px;
        border-radius: 10px;
        margin: 5px;
        transition: all 0.3s ease;
        color: #333;
        text-decoration: none;
        display: inline-block;
    }

    .quick-btn:hover {
        border-color: #4CAF50;
        color: #4CAF50;
        transform: translateY(-2px);
        text-decoration: none;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 15px;
        position: relative;
        z-index: 2;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }
</style>

<div class="schools-hero">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="hero-title">Schools Management System</h1>
            <p class="hero-subtitle">Advanced multi-school management platform with comprehensive features for educational institutions</p>
        </div>
        <div class="col-md-4 text-right">
            <i class="icon-graduation" style="font-size: 5rem; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number">{{ \App\Models\School::count() ?? 0 }}</div>
        <div class="stat-label">Active Schools</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ \App\Models\StudentRecord::count() ?? 0 }}</div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ \App\User::where('user_type', 'teacher')->count() ?? 0 }}</div>
        <div class="stat-label">Teachers</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ \App\Models\MyClass::count() ?? 0 }}</div>
        <div class="stat-label">Classes</div>
    </div>
</div>

<div class="management-cards">
    <div class="management-card">
        <div class="card-icon">
            <i class="icon-office"></i>
        </div>
        <h3 class="card-title">School Management</h3>
        <p class="card-description">Create, edit, and manage multiple schools with individual settings, configurations, and administrative controls.</p>
        <ul class="features-list">
            <li>Add new schools with detailed information</li>
            <li>Configure school-specific settings</li>
            <li>Manage school logos and branding</li>
            <li>Set academic sessions and terms</li>
            <li>Enable/disable school operations</li>
        </ul>
        <a href="{{ route('schools.index') }}" class="action-btn">
            <i class="icon-plus"></i>
            Manage Schools
        </a>
    </div>

    <div class="management-card">
        <div class="card-icon">
            <i class="icon-users"></i>
        </div>
        <h3 class="card-title">User Administration</h3>
        <p class="card-description">Comprehensive user management system for administrators, teachers, students, and parents across all schools.</p>
        <ul class="features-list">
            <li>Create and manage user accounts</li>
            <li>Assign roles and permissions</li>
            <li>Bulk user operations</li>
            <li>User profile management</li>
            <li>Account activation controls</li>
        </ul>
        <a href="{{ route('users.index') }}" class="action-btn">
            <i class="icon-user-plus"></i>
            Manage Users
        </a>
    </div>

    <div class="management-card">
        <div class="card-icon">
            <i class="icon-cog"></i>
        </div>
        <h3 class="card-title">System Configuration</h3>
        <p class="card-description">Advanced system settings and configurations to customize the platform according to your requirements.</p>
        <ul class="features-list">
            <li>Global system settings</li>
            <li>Email configurations</li>
            <li>Payment gateway settings</li>
            <li>Security configurations</li>
            <li>Backup and maintenance</li>
        </ul>
        <a href="#" class="action-btn" onclick="alert('System configuration coming soon!')">
            <i class="icon-settings"></i>
            System Settings
        </a>
    </div>
</div>

<div class="quick-actions">
    <h5><i class="icon-flash mr-2"></i>Quick Actions</h5>
    <div class="row">
        <div class="col-md-3">
            <a href="{{ route('schools.index') }}" class="quick-btn w-100 text-center">
                <i class="icon-plus d-block mb-2"></i>
                Add New School
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('users.index') }}" class="quick-btn w-100 text-center">
                <i class="icon-user-plus d-block mb-2"></i>
                Add Administrator
            </a>
        </div>
        <div class="col-md-3">
            <a href="#" class="quick-btn w-100 text-center" onclick="alert('Import feature coming soon!')">
                <i class="icon-upload d-block mb-2"></i>
                Import Data
            </a>
        </div>
        <div class="col-md-3">
            <a href="#" class="quick-btn w-100 text-center" onclick="alert('Reports feature coming soon!')">
                <i class="icon-stats-bars d-block mb-2"></i>
                View Reports
            </a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add smooth hover effects
    $('.management-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // Animate cards on scroll
    $(window).scroll(function() {
        $('.management-card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate__animated animate__fadeInUp');
            }
        });
    });
});
</script>

@endsection
