<form method="post" action="{{ route('students.promote', [$fc, $fs, $tc, $ts]) }}">
    @csrf
    <table class="table table-striped">
        <thead>
        <tr>
            <th>
                <input type="checkbox" id="select_all_students" class="form-check-input">
            </th>
            <th>#</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Current Session</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students->sortBy('user.name') as $sr)
            <tr data-school-id="{{ $sr->my_class->school_id ?? '' }}" data-from-class="{{ $sr->my_class_id }}" data-to-class="{{ $tc }}">
                <td>
                    <input type="checkbox" name="selected_students[]" value="{{ $sr->id }}" class="form-check-input">
                </td>
                <td>{{ $loop->iteration }}</td>
                <td><img class="rounded-circle" style="height: 30px; width: 30px;" src="{{ $sr->user->photo }}" alt="img"></td>
                <td>{{ $sr->user->name }}</td>
                <td>{{ $sr->session }}</td>
                <td>
                    <select class="form-control select" name="p-{{$sr->id}}" id="p-{{$sr->id}}">
                        <option value="P">Promote</option>
                        <option value="D">Don't Promote</option>
                        <option value="G">Graduated</option>
                    </select>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="text-center mt-3">
        <button class="btn btn-success"><i class="icon-stairs-up mr-2"></i> Promote Students</button>
    </div>
</form>
