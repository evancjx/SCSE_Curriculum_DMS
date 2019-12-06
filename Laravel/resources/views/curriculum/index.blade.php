@extends('layouts.app')

@section('content')
    <table id="curriculumList" class="vertical-table mx-auto mt-5 border border-dark">
        <tr>
            <th class="text-center" colspan="2">Course</th>
            <th class="text-center">AUs</th>
            <th class="text-center" colspan="2">Action</th>
        </tr>
        @foreach($course_list as $course)
        <tr>
            <td class="border border-dark border-right-0 mid-col text-right">{{$course->rep." ".$course->code}}</td>
            <td class="border border-dark border-left-0 border-right-0">
                <a href="/curriculum/{{$course->code}}">{{$course->title}}</a>
            </td>
            <td class="border border-dark border-left-0 text-center">{{$course->noAU}}</td>
            <td class="border border-dark border-right-0 action">
                <input type='button' onclick="location.href='/curriculum/{{$course->code}}/edit'" value='Update'/>
            </td>
            <td class="border border-dark border-left-0 action">
                <input type='button' onclick="location.href='/curriculum/{{$course->code}}/export'" value='Export'/>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
