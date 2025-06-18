<div class="form-group">
    <label for="branch_selector">Select Branch:</label>
    <select id="branch_selector" name="branch_id" class="form-control select-search" data-placeholder="Choose Branch...">
        <option value="">All Branches</option>
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" {{ ($user_branch == $branch->id || $selected_branch == $branch->id) ? 'selected' : '' }}>
                {{ $branch->display_name ?? $branch->name . ' (' . $branch->code . ')' }}
            </option>
        @endforeach
    </select>
</div>

<script>
        $(document).ready(function() {
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
