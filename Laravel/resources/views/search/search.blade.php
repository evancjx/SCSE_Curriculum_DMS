@extends('layouts.app')

@section('title', 'Search Curriculum')

@section('content')
    <form action="{{Route('Search Curriculum')}}" method="post">
        @csrf
        <table class="vertical-table mx-auto mt-5">
            <tr>
                <td colspan="2"><button type='submit' id='submit'>Search Curriculum</button></td>
            </tr>
            <tr class="search">
                <td class="firstColumn border border-dark">
                    <label for="searchCode">Course Code:</label>
                    <input id="searchCode" type="text" name="code" placeholder="Code">
                </td>
                <td  class="secondColumn border border-dark">
                    <label for="searchTitle">Course Title:</label>
                    <input id="searchTitle" type="text" name="title" placeholder="Title">
                </td>
            </tr>
            <tr class="search">
                <td id='searchContactHours' rowspan="3" class="firstColumn border border-dark">
                    <span class='title'>Contact Hours:</span>
                    <table>
                        <tr>
                            <td><label><input type='checkbox' name='contactType[]' value='lecture'/><span class='title'>Lecture</span></label></td>
                            <td><input id='CHlec' type="number" name='contactHours[lecture]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
                        <tr><td><label><input type='checkbox' name='contactType[]' value='tel'/><span class='title'>TEL</span></label></td>
                            <td><input id='CHtel' type="number" name='contactHours[tel]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
                        <tr><td><label><input type='checkbox' name='contactType[]' value='tutorial'/><span class='title'>Tutorial</span></label></td>
                            <td><input id='CHtut' type="number" name='contactHours[tutorial]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
                        <tr><td><label><input type='checkbox' name='contactType[]' value='lab'/><span class='title'>Lab</span></label></td>
                            <td><input id='CHlab' type="number" name='contactHours[lab]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
                        <tr><td><label><input type='checkbox' name='contactType[]' value='exampleclass'/><span class='title'>Example Class</span></label></td>
                            <td><input id='CHexc' type="number" name='contactHours[exampleclass]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
                    </table>
                </td>
                <td class="secondColumn border border-dark">
                    <label for='searchPrerequisite'>Pre-requisite: &emsp;<input type="checkbox" name="prerequisiteFor" value="True"/>For</label>
                    <input id="searchPrerequisite" type="text" name='prerequisite' placeholder="Code/Title">
                </td>
            </tr>
            <tr>
                <td class="secondColumn border border-dark">
                    <label for='searchAssessment'>Assessment:</label>
                    <input id='searchAssessment' type="text" name='assessment' placeholder="Assessment">
                </td>
            </tr>
            <tr>
                <td class="secondColumn border border-dark">
                    <label for="searchInstructor">Course Instructor:</label>
                    <input id="searchInstructor" type="text" name='instructor' placeholder="Name/Offce/Phone/Email">
                </td>
            </tr>
        </table>
    </form>

    @if(isset($data))
        @foreach($data as $search_type => $search_data)
            <table class="vertical-table mx-auto mt-5" style="width:600px">
            @switch($search_type)
                @case ('course')
                @case ('contactType')
                @case ('prerequisite')
                    @foreach($search_data as $data_header => $value)
                        @switch($data_header)
                            @case('searchCondition')
                                <tr><td colspan="3" style="border:0">{!! nl2br(e($value)) !!}</td></tr>
                                @break
                            @case('result')
                                <th class="text-center" colspan="2">Course</th><th class="text-center">AUs</th>
                                @forelse($value as $row)
                                    <tr>
                                    @foreach($row as $course_header => $value)
                                        @switch($course_header)
                                            @case('rep')
                                                <td class="mid-col border border-dark border-right-0 text-right">{{$value}}</td>
                                                <td class='border border-dark border-left-0'>
                                                @break
                                            @case('code')
                                                <a href='/curriculum/{{$value}}'>
                                                @break
                                            @case ('title')
                                                <b>{{$value}}</b></a></td>
                                                @break
                                            @case ('noAU')
                                                <td class="mid-col border border-dark text-center">{{$value}}</td>
                                                @break
                                        @endswitch
                                    @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="3">No result</td></tr>
                                @endforelse
                                @break
                        @endswitch
                    @endforeach
                    @break
                @case ('instructor')
                    @foreach($search_data as $data_header => $value)
                        @switch($data_header)
                            @case('searchCondition')
                                <tr><td colspan="2" style="border:0">{!! nl2br(e($value)) !!}</td></tr>
                                @break
                            @case('result')
                                @forelse($value as $row)
                                    @foreach($row as $instructor_header => $value)
                                        @switch($instructor_header)
                                            @case('ID')
                                            @case('office')
                                            @case('phone')
                                                @break
                                            @case('name')
                                                <tr><td class="border border-dark"><b>{{$value}}</b>
                                                @break
                                            @case('email')
                                                <a href="mailto: {{$value}}">{{$value}}</a></td></tr>
                                                @break
                                            @case('courses')
                                                <tr><td class="border border-dark"><table class="resultInnerTbl"><tr><th class="text-center">Course Code</th><th class="text-center">Course Title</th></tr>
                                                    @foreach($value as $course)
                                                        <tr>
                                                        @foreach($course as $course_header => $course_value)
                                                            @switch($course_header)
                                                                @case('rep')
                                                                    <td class="">{{$course_value}}</td>
                                                                    @break
                                                                @case('code')
                                                                    <td class=""><a href="/curriculum/{{$course_value}}">
                                                                    @break
                                                                @case('title')
                                                                    {{$course_value}}</a></td>
                                                                    @break
                                                            @endswitch
                                                        @endforeach
                                                        </tr>
                                                    @endforeach
                                                </table></td></tr>
                                                @break
                                            @endswitch
                                    @endforeach
                                @empty
                                    <tr><td colspan="3">No result</td></tr>
                                @endforelse
                                @break
                        @endswitch
                    @endforeach
                    @break
                @case ('assessment')
                    @foreach($search_data as $data_header => $value)
                        @switch($data_header)
                            @case('searchCondition')
                                <tr><td colspan="3" style="border:0">{!! nl2br(e($value)) !!}</td></tr>
                                @break
                            @case('result')
                                @forelse($value as $row)
                                    @foreach($row as $course_header => $course_value)
                                        @switch($course_header)
                                            @case('rep')
                                                <tr><td class="border border-dark">{{$course_value}}&emsp;
                                                @break
                                            @case('code')
                                                <b><a href="/curriculum/{{$course_value}}">
                                                @break
                                            @case('title')
                                                {{$course_value}}</a></b></td></tr>
                                                @break
                                            @case('assessment')
                                                <tr><td class="border border-dark"><table class=""><tr><th class="text-center">Component</th><th class="text-center">Weightage</th></tr>
                                                @foreach($course_value as $assessment)
                                                    <tr>
                                                    @foreach($assessment as $aKey => $aValue)
                                                        @switch($aKey)
                                                            @case('component')
                                                                <td class="long-col">{{$aValue}}</td>
                                                                @break
                                                            @case('weightage')
                                                                <td class="shortCol text-center">{{$aValue}}</td>
                                                                @break
                                                        @endswitch
                                                    @endforeach
                                                    </tr>
                                                @endforeach
                                                </table></td></tr>
                                        @endswitch
                                    @endforeach
                                @empty
                                    <tr><td colspan="3">No result</td></tr>
                                @endforelse
                                @break
                        @endswitch
                    @endforeach
                    @break
            @endswitch
            </table>
        @endforeach
    @endif
@endsection
