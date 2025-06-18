
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="school_selector">Select School:</label>
            <select id="school_selector" name="school_id" class="form-control select-search" data-placeholder="Choose School...">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ ($selected_school == $school->id) ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="branch_selector">Select Branch:</label>
            <select id="branch_selector" name="branch_id" class="form-control select-search" data-placeholder="Choose Branch...">
                <option value="">All Branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ ($selected_branch == $branch->id) ? 'selected' : '' }}>
                        {{ $branch->display_name ?? $branch->name . ' (' . $branch->code . ')' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#school_selector').on('change', function() {
        var schoolId = $(this).val();
        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        if(schoolId) {
            url.searchParams.set('school_id', schoolId);
            url.searchParams.delete('branch_id'); // Clear branch when school changes
        } else {
            url.searchParams.delete('school_id');
            url.searchParams.delete('branch_id');
        }

        window.location.href = url.toString();
    });

    $('#branch_selector').on('change', function() {
        var branchId = $(this).val();
        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        if(branchId) {
            url.searchParams.set('branch_id', branchId);
        } else {
            url.searchParams.delete('branch_id');
        }

        window.location.href = url.toString();
    });
});
</script>
