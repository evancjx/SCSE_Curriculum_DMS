@extends('layouts.app')

@section('title', $course->rep." ".$course->code." - ".$course->title)

@section('content')
    <table class="vertical-table mx-auto mt-5">
        <tr>
            <td class="border-0 pb-3 font-weight-bold" colspan="7">{{$course->header}}</td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">Academic Year</td>
            <td class="" colspan="2"></td>
            <td class="label border border-dark font-weight-bold" colspan="2">Semester</td>
            <td class="border border-dark" colspan="2"></td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">Authors</td>
            <td class="details border border-dark" colspan="6"></td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">Course Code</td>
            <td class="details border border-dark" colspan="6">{{$course->rep." ".$course->code}}</td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">Course Title</td>
            <td class="details border border-dark" colspan="6">{{$course->title}}</td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">Pre-requisites</td>
            <td class="details border border-dark" colspan="6">
            @if(!$course->prerequisite->isEmpty())
                @foreach($course->prerequisite as $key => $prerequisite)
                    @if($key != 0){!! nl2br(e("\r\n")) !!}@endif
                    @if($prerequisite->course == null)@continue @endif
                    {{$prerequisite->course->rep." ".$prerequisite->course->code." - ".$prerequisite->course->title}}
                @endforeach
            @else
                NIL
            @endif
            </td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">Pre-requisite for</td>
            <td class="details border border-dark" colspan="6">
            @if(!$course->prerequisiteFor->isEmpty())
                @foreach($course->prerequisiteFor as $key => $prerequisiteFor)
                    @if($key != 0){!! nl2br(e(",\r\n")) !!}@endif
                    {{$prerequisiteFor->course->rep." ".
                    $prerequisiteFor->course->code." - ".
                    $prerequisiteFor->course->title}}
                @endforeach
            @else
                NIL
            @endif
            </td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">No. of AUs</td>
            <td class="details border border-dark" colspan="6">{{$course->noAU}}</td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold">Contact Hours</td>
            @if($course->contactHour !== null)
                @foreach($contactHour as $key=>$index)
                    @if($index >= 0 && $index !== null)
                        @switch($key)
                            @case('lecture')@php $header = 'Lecture'; @endphp @break
                            @case('tel')@php $header = 'TEL'; @endphp @break
                            @case('tutorial')@php $header = 'Tutorial'; @endphp @break
                            @case('lab')@php $header = 'Lab'; @endphp @break
                            @case('exampleClass')@php $header = 'Example Class'; @endphp @break
                        @endswitch
                        <td class="ch-label text-danger border border-dark">{{$header}}</td>
                        <td class="ch-details border border-dark">{{$index}}</td>
                    @endif
                @endforeach
            @else
                <td class="ch-label text-danger border border-dark"></td>
                <td class="ch-details border border-dark"></td>
                <td class="ch-label text-danger border border-dark"></td>
                <td class="ch-details border border-dark"></td>>
                <td class="ch-label text-danger border border-dark"></td>
                <td class="ch-details border border-dark"></td>
            @endif
        </tr>
    </table>
    <table class="vertical-table mx-auto mt-5">
        <tr>
            <td class="border border-dark font-weight-bold">Course Aims</td>
        </tr>
        <tr>
            <td class="border border-dark">@if($course->objectives !== null){{$course->objectives->courseAims}}@endif</td>
        </tr>
        <tr>
            <td class="border border-dark font-weight-bold">Intended Learning Outcomes</td>
        </tr>
        <tr>
            <td class="border border-dark">
                By the end of this course, the student would be able to:<br><br>
                <ol>
                @if(!$course->learningOutcomes->isEmpty())@foreach($course->learningOutcomes as $LO)
                    <li>{{$LO->description}}</li>
                @endforeach @endif
                </ol>
            </td>
        </tr>
    </table>
    @if(!$course->content->isEmpty())
    <table class="vertical-table mx-auto mt-5 pageBreak">
        <tr>
            <td class="border border-dark border-bottom-0 font-weight-bold">
                Course Contents [{{$course->rep." ".$course->code." - ".$course->title}}]
            </td>
        </tr>
        <tr>
            <td class="border border-dark border-top-0 p-1">
                <table class="mt-3 w-100">
                    <tr>
                        <td class="border border-dark mini-col"></td>
                        <td class="border border-dark font-weight-bold">Topics</td>
                        <td class="border border-dark mid-col">@if($course->contentAtt!=null){!! nl2br(e($course->contentAtt->att1)) !!}@endif</td>
                        <td class="border border-dark mid-col">@if($course->contentAtt!=null){!! nl2br(e($course->contentAtt->att2)) !!}@endif</td>
                    </tr>
                    @foreach($course->content as $content)
                    <tr>
                        <td class="border border-dark text-right p-1">{{$content->ID}}</td>
                        <td class="border border-dark p-1">{!! html_entity_decode(nl2br(e($content->topics))) !!}</td>
                        <td class="border border-dark text-center">{{$content->contentAttDetails->details1}}</td>
                        @if($content->contentAttDetails->rowspan != 0)
                        <td class="border border-dark text-center" rowspan="{{$content->contentAttDetails->rowspan}}">
                            {!! nl2br(e($content->contentAttDetails->details2)) !!}</td>
                        @endif
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    @endif
    @if(!$course->assessment->isEmpty())
    <table class="vertical-table mx-auto mt-5 pageBreak">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold">
            Assessment [{{$course->rep." ".$course->code." - ".$course->title}}]
        </td></tr>
        <tr>
            <td class="border border-dark border-top-0 p-1">
                <table class="mt-3 w-100">
                    <tr>
                        <td class='border border-dark font-weight-bold long-col'>Component</td>
                        <td class='border border-dark font-weight-bold mid-col'>Course LO Tested</td>
                        <td class='border border-dark font-weight-bold mid-col'>Related Programme LO or Graduate Attributes</td>
                        <td class='border border-dark font-weight-bold mid-col'>Weightage</td>
                        <td class='border border-dark font-weight-bold mid-col'>Team / Individual</td>
                        <td class='border border-dark font-weight-bold med-col'>Assessment Rubrics</td>
                    </tr>
                    @foreach($course->assessment as $assessment)
                        <tr>
                            <td class='border border-dark'>{{$assessment->component}}</td>
                            <td class='border border-dark'>
                                @foreach($assessment->assessment_lo as $key => $lo_tested)
                                    {{$lo_tested->lo_ID}}@if($key < ($assessment->assessment_lo->count()-1)){{','}}@endif
                                @endforeach
                            </td>
                            <td class='border border-dark'>
                                @foreach($assessment->assessment_gradattr as $key => $related_gradAttr)
                                {{$related_gradAttr->gradAttrID}}@if($key < ($assessment->assessment_gradattr->count()-1)){{','}}@endif
                                @endforeach
                            </td>
                            <td class='border border-dark'>{{$assessment->weightage}}%</td>
                            <td class='border border-dark text-capitalize'>{{$assessment->assessment_category->category}}</td>
                            <td class='border border-dark'>
                                @if($assessment->rubrics !== null)
                                {{$assessment->rubrics->description}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    @endif
    @if(!$course->learningOutcomes->isEmpty() and !$course->learningOutcomes[0]->grad_attr->isEmpty())
    <table class="horizontal-table landscape mx-auto mt-5 pageBreak">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold">
            Mapping of Course SLOs to EAB Graduate Attributes
        </td></tr>
        <tr>
            <td id="mappingGradAttr" class="border border-dark border-top-0 p-1">
                <table class="w-100">
                    <!--First Row  -->
                    <tr>
                        <td class='long-col border border-dark font-weight-bold' rowspan="2">Course Student Learning Outcomes</td>
                        <td class='short-col border border-dark font-weight-bold text-center' rowspan="2">Cat</td>
                        <td class='long-col border border-dark font-weight-bold' colspan="{{$grad_attr->count()}}">EAB's {{$grad_attr->count()}} Graduate Attributes*</td>
                        <td class='mid-col border border-dark font-weight-bold' colspan="2">EAB's CE/CS Requirement</td>
                    </tr>
                    <!-- Second Row -->
                    <tr>
                        @foreach($grad_attr as $key => $value)
                        <td class='short-col border border-dark text-center'>{{$value->ID}}</td>
                        @endforeach
                        <td class='short-col border border-dark text-center'>CE</td><td class='short-col border border-dark text-center'>CS</td>
                    </tr>
                    <!-- Third Row -->
                    <tr>
                        <td class='long-col border border-dark header'>{{$course->rep." ".$course->code." - ".$course->title}}</td>
                        <td class='short-col border border-dark'></td>
                        @foreach($lo_ga_count as $key => $value)
                        <td class='short-col border border-dark text-center'>
                        @switch(1)
                            @case($value >= 0.75)<img src='{{ asset('assets/full-dot.png') }}' style='width:17px' alt="">@break
                            @case($value >= 0.5)<img src='{{ asset('assets/half-dot.png') }}' style='width:17px' alt="">@break
                            @case($value >= 0.25)<img src='{{ asset('assets/empty-dot.png') }}' style='width:17px' alt="">@break
                            @default<img src='{{ asset('assets/blank-dot.png') }}' style='width:17px' alt="">
                        @endswitch
                        </td>
                        @endforeach
                        @if(in_array('CE', $course->codeSplit))
                            <td class='short-col border border-dark text-center'><img src='{{ asset('assets/full-dot.png') }}' style='width:17px' alt=""></td>
                        @else <td class='short-col border border-dark'></td>
                        @endif
                        @if(in_array('CZ', $course->codeSplit))
                            <td class='short-col border border-dark text-center'><img src='{{ asset('assets/full-dot.png') }}' style='width:17px' alt=""></td>
                        @else<td class='short-col border border-dark'></td>
                        @endif
                    </tr>
                    <!-- Forth Row -->
                    <tr>
                        <td class='long-col border border-dark font-weight-bold' >Overall Statement</td>
                        <td class='short-col border border-dark' colspan="{{$grad_attr->count()+1}}">{!! nl2br(e($course->objectives->courseAims)) !!}</td>
                        <td class='short-col border border-dark' colspan="2"></td>
                    </tr>
                    <!-- Learning Outcomes Rows -->
                    @foreach($course->learningOutcomes as $LO)
                    <tr>
                        <td class='long-col border border-dark'>{!! nl2br(e($LO->description)) !!}</td>
                        <td class='short-col border border-dark' colspan="{{$grad_attr->count()+1}}">
                            @foreach($LO->grad_attr as $key => $lo_attr)
                            {{$lo_attr->gradAttrID}}@if($key < ($LO->grad_attr->count()-1)){{','}}@endif
                            @endforeach
                        </td>
                        <td class='short-col border border-dark' colspan="2"></td>
                    </tr>
                    @endforeach
                    <!-- Fifth Row -->
                    <tr>
                        <td class="border border-dark" colspan="{{$grad_attr->count()+4}}">
                            <table >
                                <tr><td colspan="2">Legend:</td></tr>
                                <tr>
                                    <td><img alt='' src='{{ asset('assets\full-dot.png') }}' style='width:17px'></td>
                                    <td>Fully consistent (contributes to more than 75% of Student Learning Outcomes)</td>
                                </tr>
                                <tr>
                                    <td><img alt='' src='{{ asset('assets\half-dot.png') }}' style='width:17px'></td>
                                    <td>Partially consistent (contributes to about 50% of Student Learning Outcomes)</td>
                                </tr>
                                <tr>
                                    <td><img alt='' src='{{ asset('assets\empty-dot.png') }}' style='width:17px'></td>
                                    <td>Weakly consistent (contributes to about 25% of Student Learning Outcomes)</td>
                                </tr>
                                <tr>
                                    <td>Blank</td>
                                    <td>Not related to Student Learning Outcomes</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="horizontal-table landscape mx-auto mt-5 pageBreak border border-dark">
        <tr><td class="border border-dark" colspan="2">The graduate attributes as stipulated by the EAB, are:</td></tr>
        @foreach($grad_attr as $row)
            <tr>
                <td class="p-2">({{$row->ID}})</td>
                <td class=""><b>{{$row->main}}</b>{{': '.$row->description}}</td>
            </tr>
        @endforeach
    </table>
    @endif
    @if($course->formativeFeedback !== null)
    <table class="vertical-table mx-auto mt-5 pageBreak">
        <tr><td class="border border-dark font-weight-bold">
             Formative Feedback [{{$course->rep." ".$course->code." - ".$course->title}}]
        </td></tr>
        <tr>
            <td class="border border-dark">{!! nl2br(e($course->formativeFeedback->description)) !!}</td>
        </tr>
    </table>
    @endif
    @if(!$course->approach->isEmpty())
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr><td class="border-bottom-0 font-weight-bold">
            Learning and Teaching approach</td></tr>
        <tr>
            <td class="p-1">
                <table class="border border-dark w-100">
                    <tr>
                        <td class="font-weight-bold border border-dark w-25">Approach</td>
                        <td class="font-weight-bold border border-dark">How does this approach support students in achieving the learning outcomes?</td>
                    </tr>
                    @foreach($course->approach as $approach)
                    <tr>
                        <td class="border border-dark">{{$approach->approach}}</td>
                        <td class="border border-dark">{{$approach->description}}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    @endif
    @if(!$course->reference->isEmpty())
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr>
            <td class="border border-dark font-weight-bold">Reading and References</td>
        </tr>
        <tr>
            <td>This course will not use any specific text book. The following books and websites will be used as references materials.</td>
        </tr>
        <tr>
            <td>
                <ol>
                    @foreach($course->reference as $reference)
                    <li>{{$reference->description}}</li>
                    @endforeach
                </ol>
            </td>
        </tr>
    </table>
    @endif
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr>
            <td class="font-weight-bold border-bottom border-dark">Course Policies and Student Responsibilities</td>
        </tr>
        <tr>
            <td>
                @foreach($common as $value)
                    @if($value->title == "Course Policies And Student Responsibilities")
                        {!! html_entity_decode(nl2br(e($value->description))) !!}
                    @endif
                @endforeach
            </td>
        </tr>
    </table>
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr>
            <td class="font-weight-bold border-bottom border-dark">Academic Integrity</td>
        </tr>
        <tr>
            <td>
                @foreach($common as $value)
                    @if($value->title == "Academic Integrity")
                        {!! nl2br(e($value->description)) !!}
                    @endif
                @endforeach
            </td>
        </tr>
    </table>
    @if(!$course->instructor->isEmpty())
    <table class="vertical-table mx-auto mt-5 border border-dark pageBreak">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold">
            Course Instructor [{{$course->rep." ".$course->code." - ".$course->title}}]
        </td></tr>
        <tr>
            <td class="p-1">
                <table class="border border-dark mx-auto w-100">
                    <tr>
                        <th class="border border-dark w-25">Instructor</th>
                        <th class="border border-dark w-25">Office Location</th>
                        <th class="border border-dark w-25">Phone</th>
                        <th class="border border-dark w-25">Email</th>
                    </tr>
                    @foreach($course->instructor as $instructor)
                    <tr>
                        <td class="border border-dark w-25">{{$instructor->academicStaff->name}}</td>
                        <td class="border border-dark w-25">{{$instructor->academicStaff->office}}</td>
                        <td class="border border-dark w-25">{{$instructor->academicStaff->phone}}</td>
                        <td class="border border-dark w-25">{{$instructor->academicStaff->email}}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    @endif
    @if(!$course->schedule->isEmpty())
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold">
            Weekly Schedule
        </td></tr>
        <tr>
            <td class="p-1">
                <table class="border border-dark mx-auto w-100">
                    <tr>
                        <th class="border border-dark short-col">Week</th>
                        <th class="border border-dark">Topic</th>
                        <th class="border border-dark mid-col">Course LO</th>
                        <th class="border border-dark mid-col">Readings</th>
                        <th class="border border-dark med-col">Example Activities</th>
                    </tr>
                    @foreach($course->schedule as $schedule)
                    <tr>
                        <td class="border border-dark short-col text-right">{{$schedule->weekID}}</td>
                        <td class="border border-dark">{{$schedule->topic}}</td>
                        <td class="border border-dark">
                        @foreach($schedule->LO as $key => $LO)
                        {{$LO->loID}}@if($key < ($schedule->LO->count()-1)){{','}}@endif
                        @endforeach
                        </td>
                        <td class="border border-dark">{{$schedule->readings}}</td>
                        <td class="border border-dark">{{$schedule->activities}}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    @endif
    @if(!$course->appendix->isEmpty())
    <table class="vertical-table mx-auto mt-5 pageBreak">
        @foreach($course->appendix as $appendix)
        <tr>
            <td class="font-weight-bold">Appendix {{$appendix->ID}}: {{$appendix->header}}</td>
        </tr>
        <tr>
            <td>{!! nl2br(e($appendix->description)) !!}</td>
        </tr>
        @if(!$appendix->criteria->isEmpty())
        <tr>
            <td>
                <table class="w-100 border border-dark">
                    <tr>
                        <td class="w-25 border border-dark" rowspan="2">{!! nl2br(e("Criteria\r\nfor Appendix")) !!} {{$appendix->ID}}</td>
                        <td class="w-75 border border-dark" colspan="3">Standards</td>
                    </tr>
                    <tr>
                        <td class="w-25 border border-dark mid-col">{!! nl2br(e("Fail Standard\r\n(0-39%)")) !!}</td>
                        <td class="w-25 border border-dark mid-col">{!! nl2br(e("Pass Standard\r\n(40-80%)")) !!}</td>
                        <td class="w-25 border border-dark mid-col">{!! nl2br(e("High Standard\r\n(81-100%)")) !!}</td>
                    </tr>
                    @foreach($appendix->criteria as $criteria)
                    <tr>
                        <td class="w-25 border border-dark mid-col">{!! nl2br(e($criteria->header)) !!}</td>
                        <td class="w-25 border border-dark mid-col">{!! nl2br(e($criteria->fail)) !!}</td>
                        <td class="w-25 border border-dark mid-col">{!! nl2br(e($criteria->pass)) !!}</td>
                        <td class="w-25 border border-dark mid-col">{!! nl2br(e($criteria->high)) !!}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        @endif
        @endforeach
    </table>
    @endif
@endsection
