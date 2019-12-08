@extends('layouts.app')

@section('title', $title)

@section('content')
<form action="{{Route('Store Curriculum')}}" method="post">
    @csrf
    <table class="vertical-table mx-auto mt-5">
        <tr>
            <td>
                <button type="submit">Submit</button>
            </td>
        </tr>
        <tr>
            <td class="border-0 pb-3 font-weight-bold" colspan="7">{{$func}} Curriculum</td>
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
            <td class="label border border-dark font-weight-bold"><label for="course_code">Course Code</label></td>
            <td class="details border border-dark" colspan="4">
                <input id="course_code" type="text" name="course[code]" value="@if(isset($course)){{$course->code}}@endif" required autofocus/>
            </td>
            <td class="details border border-dark" colspan="4">
                <label class="font-weight-bold">
                    <input id="course_CE" class="m-2 course_rep" type="checkbox" name="course[rep][]" value="CE" @if(isset($course) && in_array('CE', $course->codeSplit)) checked @elseif(isset($course)) @else checked @endif/>CE
                </label>
                <label class="font-weight-bold">
                    <input id="course_CZ" class="m-2 course_rep" type="checkbox" name="course[rep][]" value="CZ" @if(isset($course) && in_array('CZ', $course->codeSplit)) checked @elseif(isset($course)) @else checked @endif/>CZ
                </label>
            </td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold"><label for="course_title">Course Title</label></td>
            <td class="details border border-dark" colspan="6">
                <input id="course_title" class="w-100" type="text" name="course[title]" value="@if(isset($course)){{$course->title}}@endif" required/>
            </td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold"><label for="prerequisite">Pre-requisites</label></td>
            <td class="details border border-dark" colspan="6">
                <input id="prerequisite" class="w-100" type="text" name="course[prerequisite]"
                       value="@if(isset($course) && !$course->prerequisite->isEmpty())@foreach($course->prerequisite as $key=>$prerequisite)@if($key>0), @endif{{$prerequisite->code}}@endforeach @else{{'NIL'}}@endif"/>
            </td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold"><label for="noAU">No. of AUs</label></td>
            <td class="details border border-dark" colspan="6">
                <input id="noAU" class="" type="text" name="course[noAU]" value="@if(isset($course)){{$course->noAU}}@endif"/>
            </td>
        </tr>
        <tr>
            <td class="label border border-dark font-weight-bold"><label for="contactType">Contact Hours</label></td>
            @if(isset($course) && !empty($contactHour))
                @php $i = 0 @endphp
                @foreach($contactHour as $key=>$index)
                    @if($index >= 0 && $index !== null)
                        <td class="ch-label border border-dark">
                            <select id="contact_type_select{{($i+=1)}}" class="w-100 contactType" name="course[contactType][]">
                                <option @if($key === 'lecture') selected @endif>Lecture</option>
                                <option @if($key === 'tel') selected @endif>TEL</option>
                                <option @if($key === 'tutorial') selected @endif>Tutorial</option>
                                <option @if($key === 'lab') selected @endif>Lab</option>
                                <option @if($key === 'exampleClass') selected @endif>Example Class</option>
                            </select>
                        </td>
                        <td class="ch-details border border-dark">
                            <input id="contactType" class="w-100" type="text" name="course[contactHour][]" value="{{$index}}">
                        </td>
                    @endif
                @endforeach
            @else
                @for($i = 1; $i <= 3; $i++)
                    <td class="ch-label border border-dark">
                        <select id="contact_type_{{$i}}" class="w-100 contactType" name="course[contactType][]">
                            <option selected></option>
                            <option value=lecture>Lecture</option>
                            <option value="tel">TEL</option>
                            <option value="tutorial">Tutorial</option>
                            <option value="lab">Lab</option>
                            <option value="exampleClass">Example Class</option>
                        </select>
                    </td>
                    <td class="ch-details border border-dark">
                        <input id="contactType" class="w-100" type="text" name="course[contactHour][]" value="">
                    </td>
                @endfor
            @endif
        </tr>
    </table>
    {{--Objectives--}}
    <table class="vertical-table mx-auto mt-5">
        <tr>
            <td class="border border-dark font-weight-bold"><label for="course_aims">Course Aims</label></td>
        </tr>
        <tr>
            <td class="border border-dark">
                <textarea id="course_aims" name="objectives[aims]" class="w-100" type="text">@if(isset($course) && $course->objectives !== null){{$course->objectives->courseAims}}@endif</textarea>
            </td>
        </tr>
        <tr>
            <td class="border border-dark font-weight-bold"><label for="iLO1">Intended Learning Outcomes</label></td>
        </tr>
        <tr>
            <td class="border border-dark">
                By the end of this course, the student would be able to:
                <table id="LOs" class="w-100">
                    @if(isset($course) && !$course->learningOutcomes->isEmpty())
                        @foreach($course->learningOutcomes as $key=>$LO)
                            <tr id="LO{{$LO->ID}}" class="LO_row">
                                <td class="border border-dark"><textarea id="iLO{{$LO->ID}}" class="w-100 loTextArea" name="objectives[LO][{{$LO->ID}}][description]" placeholder="Learning Outcomes">{{$LO->description}}</textarea></td>
                                @if($key === 0)
                                    <td class="border border-dark action"><input id="addNewLO" type="button" value="Add"/></td>
                                @else
                                    <td class="border border-dark action"><input id="LO{{$LO->ID}}" class="btn-danger LO_remove" type="button" name="remove" value="Del" /></td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr id="LO1" class="LO_row">
                            <td class="border border-dark"><textarea id="iLO1" class="w-100 loTextArea" name="objectives[LO][1][description]" placeholder="Learning Outcomes"></textarea></td>
                            <td class="border border-dark action"><input id="addNewLO" type="button" value="Add"/></td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    {{--Content--}}
    <table class="horizontal-table mx-auto mt-5">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold">Course Contents</td></tr>
        <tr>
            <td class="border border-dark border-top-0 p-1">
                <table id="contents" class="mt-3 w-100">
                    <tr>
                        <td class="border border-dark mini-col"></td>
                        <td class="border border-dark font-weight-bold">Topics</td>
                        <td class="border border-dark med-col">
                            <textarea class="contentAtt" name="content[att1]" placeholder="Attribute 1">@if(isset($course) && $course->contentAtt!==null){{$course->contentAtt->att1}}@endif</textarea>
                        </td>
                        <td class="border border-dark med-col">
                            <textarea class="contentAtt" name="content[att2]" placeholder="Attribute 2">@if(isset($course) && $course->contentAtt!==null){{$course->contentAtt->att2}}@endif</textarea>
                        </td>
                        <td class="border border-dark action"></td>
                    </tr>
                    @if(isset($course) && !$course->content->isEmpty())
                        @foreach($course->content as $key=>$content)
                            <tr id="topic{{$content->ID}}" class="content_topic">
                                <td class="border border-dark short-col">
                                    <input id="SN" type="text" name="content[topic][{{$content->ID}}][ID]" placeholder="S/N" value="{{$content->ID}}"/>
                                </td>
                                <td class="border border-dark">
                                    <textarea class="description" name="content[topic][{{$content->ID}}][description]" placeholder="Topic description">{{$content->topics}}</textarea>
                                </td>
                                <td class="border border-dark">
                                    <textarea class="" name="content[topic][{{$content->ID}}][details1]" placeholder="Topic details">@if(isset($content->contentAttDetails->details1)){{$content->contentAttDetails->details1}}@endif</textarea>
                                </td>
                                <td class="border border-dark att2" @if($content->contentAttDetails->rowspan == 1) rowspan="1" @elseif($content->contentAttDetails->rowspan == 2) rowspan="2" @elseif($content->contentAttDetails->rowspan == 0) style="display:none" @endif>
                                    <textarea class="" name="content[topic][{{$content->ID}}][details2]" placeholder="Topic details">@if(isset($content->contentAttDetails->details2)){{$content->contentAttDetails->details2}}@endif</textarea>
                                    <label><input id="topic{{$content->ID}}" class="attDetailMerge" type="checkbox" name="content[merge][]" value="{{$content->ID}}" @if($content->contentAttDetails->rowspan == 2) checked @endif/> Merge bottom</label>
                                </td>
                                <td class="border border-dark">
                                    @if($key > 0)
                                        <input id="topic{{$content->ID}}" class="btn-danger content_remove" type="button" value="Del" />
                                    @else
                                        <input id="addNewTopic" type="button" value="Add"/>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="topic1" class="content_topic">
                            <td class="border border-dark short-col">
                                <input id="SN" type="text" name="content[topic][1][ID]" placeholder="S/N" value="1"/>
                            </td>
                            <td class="border border-dark">
                                <textarea class="description" name="content[topic][1][description]" placeholder="Topic description"></textarea>
                            </td>
                            <td class="border border-dark">
                                <textarea class="" name="content[topic][1][details1]" placeholder="Topic details"></textarea>
                            </td>
                            <td class="border border-dark att2">
                                <textarea class="" name="content[topic][1][details2]" placeholder="Topic details"></textarea>
                                <label><input id="topic1" class="attDetailMerge" type="checkbox" name="content[merge][]" value="1"/> Merge bottom</label>
                            </td>
                            <td class="border border-dark">
                                <input id="addNewTopic" type="button" value="Add"/>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    {{--Assessment--}}
    <table class="horizontal-table mx-auto mt-5 pageBreak">
        <tr>
            <td class="border border-dark border-bottom-0 font-weight-bold">Assessment</td>
        </tr>
        <tr>
            <td class="border border-dark border-top-0 p-1">
                <table id="assessment" class="mt-3 w-100">
                    <tr>
                        <td class='border border-dark font-weight-bold long-col'>Component</td>
                        <td class='border border-dark font-weight-bold mid-col'>Course LO Tested</td>
                        <td class='border border-dark font-weight-bold long-col'>Related Programme LO or Graduate Attributes</td>
                        <td class='border border-dark font-weight-bold mid-col'>Weightage</td>
                        <td class='border border-dark font-weight-bold med-col'>Team / Individual</td>
                        <td class='border border-dark font-weight-bold med-col'>Assessment Rubrics</td>
                        <td class="border border-dark action"></td>
                    </tr>
                    @if(isset($course) && !$course->assessment->isEmpty())
                        @foreach($course->assessment as $key=>$assessment)
                            <tr id="assessment{{$assessment->ID}}" class="assessment_row">
                                <td class="border border-dark">
                                    <textarea name="assessment[{{$assessment->ID}}][title]" placeholder="Component">{{$assessment->component}}</textarea>
                                </td>
                                <td class="border border-dark text-center LO_col">
                                    @foreach($course->learningOutcomes as $LO_key=>$LO)
                                        <label><input id="a{{$assessment->ID}}LO{{($LO_key+1)}}" type="checkbox" name="assessment[{{$assessment->ID}}][LO][]" value="{{$LO->ID}}" @if(in_array($LO->ID, $assessment->LOs)) checked @endif/>&emsp;{{$LO->ID}}<br></label>
                                    @endforeach
                                </td>
                                <td class="border border-dark pl-3 gradAttrCol">
                                    <span class="text-primary">Hover mouse over text for more info.</span><br/>
                                    @foreach($grad_attr as $element)
                                        <label class="cbGradAttrLbl">
                                        <input type="checkbox" class="cbGradAttr" name="assessment[{{$assessment->ID}}][gradAttr][]" value="{{$element->ID}}" @if(in_array($element->ID, $assessment->gradAttr)) checked @endif/>&nbsp;
                                            <span title="{{$element->main}}&#13;&#10;{{$element->description}}">({{$element->ID}})&emsp;{{$element->main}}</span>
                                        </label><br>
                                    @endforeach
                                </td>
                                <td class="border border-dark weightCol">
                                    <input type="text" id="w{{$assessment->ID}}" class="assessmentWeight" name="assessment[{{$assessment->ID}}][weight]" placeholder="Percentage" value="{{$assessment->weightage}}"/>
                                </td>
                                <td class="border border-dark pl-3">
                                    <input id="assessment{{$assessment->ID}}Individual" type="radio" name="assessment[{{$assessment->ID}}][category]" value="individual" @if($assessment->category == 'individual') checked @endif/>
                                    <label for="assessment{{$assessment->ID}}Individual">Individual</label><br>
                                    <input id="assessment{{$assessment->ID}}Team" type="radio" name="assessment[{{$assessment->ID}}][category]" value="team" @if($assessment->category == 'team') checked @endif/>
                                    <label for="assessment{{$assessment->ID}}Team">Team</label>
                                </td>
                                <td class="border border-dark rubricsCol">
                                    <textarea name="assessment[{{$assessment->ID}}][rubrics]" placeholder="Rubrics info to be appended at the end of the document">@if(isset($assessment->rubrics->description)){{$assessment->rubrics->description}}@endif</textarea>
                                </td>
                                <td class="border border-dark">
                                    @if($key > 0)
                                        <input id="assessment{{$assessment->ID}}" class="btn-danger assessment_remove" type="button"  value="Del">
                                    @else
                                        <input id="addNewAssessment" type="button" value="Add"/>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="assessment1" class="assessment_row">
                            <td class="border border-dark">
                                <textarea name="assessment[1][title]" placeholder="Component"></textarea>
                            </td>
                            <td class="border border-dark text-center LO_col"></td>
                            <td class="border border-dark pl-3 gradAttrCol"></td>
                            <td class="border border-dark weightCol">
                                <input type="text" id="w1" class="assessmentWeight" name="assessment[1][weight]" placeholder="Percentage"/>
                            </td>
                            <td class="border border-dark pl-3">
                                <input id="assessment1Individual" type="radio" name="assessment[1][category]" value="individual" checked/>
                                <label for="assessment1Individual">Individual</label><br>
                                <input id="assessment1Team" type="radio" name="assessment[1][category]" value="team"/>
                                <label for="assessment1Team">Team</label>
                            </td>
                            <td class="border border-dark rubricsCol">
                                <textarea name="assessment[1][rubrics]" placeholder="Rubrics info to be appended at the end of the document"></textarea>
                            </td>
                            <td class="border border-dark">
                                <input id="addNewAssessment" type="button" value="Add"/>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    {{--LOs to Grad Attr--}}
    <table class="horizontal-table mx-auto mt-5 pageBreak">
        <tr>
            <td class="border border-dark border-bottom-0 font-weight-bold">Mapping of Course SLOs to EAB Graduate Attributes</td>
        </tr>
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
                        <td class='short-col border border-dark text-center'>CE</td><td class='short-col border border-dark text-center'>CZ</td>
                    </tr>
                    <!-- Third Row -->
                    <tr>
                        <td id="courseCodeTitle" class='long-col border border-dark header'>@if(isset($course)) {{$course->rep." ".$course->code." - ".$course->title}} @endif</td>
                        <td class='short-col border border-dark'></td>
                        @foreach($grad_attr as $key => $value)
                        <td class='short-col border border-dark text-center'>
                            <img id="grad_attr_{{$value->ID}}" class="w-25 text-right" src='{{ asset('assets/blank-dot.png') }}'/>
                        </td>
                        @endforeach
                        <td id="rep_CE" class='short-col border border-dark text-center'>@if(isset($course) and in_array('CE', $course->codeSplit)) <img src='{{ asset('assets/full-dot.png') }}' style='width:17px' alt=""> @endif</td>
                        <td id="rep_CZ" class='short-col border border-dark text-center'>@if(isset($course) and in_array('CZ', $course->codeSplit)) <img src='{{ asset('assets/full-dot.png') }}' style='width:17px' alt=""> @endif</td>
                    </tr>
                    <!-- Forth Row -->
                    <tr id="">
                        <td class='long-col border border-dark font-weight-bold'>Overall Statement</td>
                        <td id="mapping_overall_statement" class='border border-dark' colspan="{{$grad_attr->count()+1}}">@if(isset($course) and $course->objectives !== null){!! nl2br(e($course->objectives->courseAims)) !!}@endif</td>
                        <td class='short-col border border-dark' colspan="2"></td>
                    </tr>
                    <!-- Learning Outcomes Rows -->
                    @if(isset($course) and !$course->learningOutcomes->isEmpty())
                        @foreach($course->learningOutcomes as $key => $learningOutcome)
                            <tr id="rowMapLO{{$learningOutcome->ID}}" class="LO_gradAttr">
                                <td id="mapLO{{$learningOutcome->ID}}" class="long-col border border-dark">
                                    {{$learningOutcome->description}}
                                </td>
                                <td class="border border-dark mapLOGradAttr" colspan="{{($grad_attr->count())+1}}">
                                    <span class="text-primary">Hover mouse over text for more info.</span><br/>
                                    @foreach($grad_attr as $index => $element)
                                        <label class="cbGradAttrLbl">
                                            <input id="{{$learningOutcome->ID.$element->ID}}" type="checkbox" class="cbGradAttr" name="objectives[LO][{{$learningOutcome->ID}}][GradAttr][]" value="{{$element->ID}}" @if(in_array($element->ID, $learningOutcome->gradAttr)) checked @endif/>
                                            <span title="{{$element->main}}&#13;&#10;{{$element->description}}">&nbsp;{{$element->ID}}&emsp;&emsp;</span>
                                        </label>
                                    @endforeach
                                </td>
                                <td colspan="2" class="short-col border border-dark"></td>
                            </tr>
                        @endforeach
                    @endif
                    <!-- Fifth Row -->
                    <tr id="mapGradAttrLegend">
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
    {{--Formative Feedback--}}
    <table class="vertical-table mx-auto mt-5">
        <tr><td class="border border-dark font-weight-bold">
                Formative Feedback
            </td></tr>
        <tr>
            <td id="formativeFeedback" class="border border-dark">
                <textarea name="formativeFeedback" placeholder="Formative Feedback">@if(isset($course) and isset($course->formativeFeedback)){{$course->formativeFeedback->description}}@endif</textarea>
            </td>
        </tr>
    </table>
    {{--Approach--}}
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr><td class="border-bottom-0 font-weight-bold">
                Learning and Teaching approach</td></tr>
        <tr>
            <td class="p-1">
                <table id="approach" class="border border-dark w-100">
                    <tr>
                        <td class="font-weight-bold border border-dark w-25">Approach</td>
                        <td class="font-weight-bold border border-dark">How does this approach support students in achieving the learning outcomes?</td>
                        <td class="border border-dark action"></td>
                    </tr>
                    @if(isset($course) and !$course->approach->isEmpty())
                        @foreach($course->approach as $key=>$approach)
                            <tr id="approach{{$approach->ID}}" class="approach_row">
                                <td class="border border-dark">
                                    <textarea name="approach[{{$approach->ID}}][header]" placeholder="Approach Header">{{$approach->approach}}</textarea>
                                </td>
                                <td class="border border-dark">
                                    <textarea name="approach[{{$approach->ID}}][description]" placeholder="Approach Description">{{$approach->description}}</textarea>
                                </td>
                                <td class="border border-dark action">
                                @if($key > 0)
                                    <input id="approach{{$approach->ID}}" class="btn-danger approach_remove" type="button" value="Del"/>
                                @else
                                    <input id="addNewApproach" type="button" value="Add"/>
                                @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                    <tr id="approach1" class="approach_row">
                        <td class="border border-dark">
                            <textarea name="approach[1][header]" placeholder="Approach Header"></textarea>
                        </td>
                        <td class="border border-dark">
                            <textarea name="approach[1][description]" placeholder="Approach Description"></textarea>
                        </td>
                        <td class="border border-dark action">
                            <input id="addNewApproach" type="button" value="Add"/>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    {{--References--}}
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr><td class="border border-dark font-weight-bold">Reading and References</td></tr>
        <tr>
            <td>This course will not use any specific text book. The following books and websites will be used as references materials.</td>
        </tr>
        <tr>
            <td>
                <table id="reference" class="w-100">
                    @if(isset($course) and !$course->reference->isempty())
                        @foreach($course->reference as $key=>$reference)
                            <tr id="reference{{$reference->ID}}" class="reference_row">
                                <td class="border border-dark">
                                    <textarea name="reference[]" placeholder="References Details">{{$reference->description}}</textarea>
                                </td>
                                <td class="border border-dark action">
                                    @if($key > 0)
                                        <input id="reference{{$reference->ID}}" class="btn-danger reference_remove" type="button" value="Del"/>
                                    @else
                                        <input id="addNewReference" type="button" value="Add"/>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="reference1" class="reference_row">
                            <td class="border border-dark">
                                <textarea name="reference[]" placeholder="References Details"></textarea>
                            </td>
                            <td class="border border-dark action">
                                <input id="addNewReference" type="button" value="Add"/>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    {{--Instructor--}}
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold">Course Instructor</td></tr>
        <tr>
            <td class="p-1">
                <table id="instructor" class="border border-dark mx-auto w-100">
                    <tr>
                        <th class="border border-dark w-25">Instructor</th>
                        <th class="border border-dark w-25">Office Location</th>
                        <th class="border border-dark w-25">Phone</th>
                        <th class="border border-dark w-25">Email</th>
                        <td class="border border-dark action"></td>
                    </tr>
                    @if(isset($course) and !$course->instructor->isempty())
                        @foreach($course->instructor as $key=>$instructor)
                            <tr id="instructor{{$key+1}}" class="instructor_row">
                                <td class="border border-dark w-25">
                                    <input type="text" name="instructor[{{$key+1}}][name]" placeholder="Name" value="{{$instructor->academicStaff->name}}"/>
                                </td>
                                <td class="border border-dark w-25">
                                    <input type="text" name="instructor[{{$key+1}}][office]" placeholder="Office Location" value="{{$instructor->academicStaff->office}}"/>
                                </td>
                                <td class="border border-dark w-25">
                                    <input type="text" name="instructor[{{$key+1}}][phone]" placeholder="Phone" value="{{$instructor->academicStaff->phone}}"/>
                                </td>
                                <td class="border border-dark w-25">
                                    <input type="text" name="instructor[{{$key+1}}][email]" placeholder="Email" value="{{$instructor->academicStaff->email}}"/>
                                </td>
                                <td>
                                    @if($key > 0)
                                        <input id="instructor{{$key+1}}" class="btn-danger instructor_remove" type="button" value="Del"/>
                                    @else
                                        <input id="addNewInstructor" type="button" value="Add"/>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="instructor1" class="instructor_row">
                            <td class="border border-dark w-25">
                                <input type="text" name="instructor[0][name]" placeholder="Name" />
                            </td>
                            <td class="border border-dark w-25">
                                <input type="text" name="instructor[0][office]" placeholder="Office Location" />
                            </td>
                            <td class="border border-dark w-25">
                                <input type="text" name="instructor[0][phone]" placeholder="Phone" />
                            </td>
                            <td class="border border-dark w-25">
                                <input type="text" name="instructor[0][email]" placeholder="Email" />
                            </td>
                            <td>
                                <input id="addNewInstructor" type="button" value="Add"/>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    {{--Schedule--}}
    <table class="horizontal-table mx-auto mt-5 border border-dark pageBreak">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold">Weekly Schedule</td></tr>
        <tr>
            <td class="p-1">
                <table id="schedule" class="border border-dark mx-auto w-100">
                    <tr>
                        <th class="border border-dark short-col">Week</th>
                        <th class="border border-dark">Topic</th>
                        <th class="border border-dark mid-col">Course LO</th>
                        <th class="border border-dark med-col">Readings</th>
                        <th class="border border-dark med-col">Example Activities</th>
                        <th class="border border-dark action"></th>
                    </tr>
                    @if(isset($course) and !$course->schedule->isempty())
                        @foreach($course->schedule as $key=>$schedule)
                            <tr id="schedule{{$schedule->weekID}}" class="schedule_row">
                                <td class="border border-dark short-col text-right">
                                    <input type="text" name="schedule[{{$schedule->weekID}}][week]" placeholder="Week" value="{{$schedule->weekID}}"/>
                                </td>
                                <td class="border border-dark topic">
                                    <textarea name="schedule[{{$schedule->weekID}}][topic]" placeholder="Topic">{{$schedule->topic}}</textarea>
                                </td>
                                <td class="border border-dark LO_col text-center">
                                    <!-- LO -->
                                    @foreach($course->learningOutcomes as $LO_key=>$LO)
                                        <label><input id="s{{$schedule->weekID}}LO{{($LO_key+1)}}" type="checkbox" name="schedule[{{$schedule->weekID}}][LO][]" value="{{$LO->ID}}" @if(in_array($LO->ID, $schedule->LO)) checked @endif/>&emsp;{{$LO->ID}}<br></label>
                                    @endforeach
                                </td>
                                <td class="border border-dark">
                                    <textarea name="schedule[{{$schedule->weekID}}][readings]" placeholder="Readings">{{$schedule->readings}}</textarea>
                                </td>
                                <td class="border border-dark">
                                    <textarea name="schedule[{{$schedule->weekID}}][activities]" placeholder="Activities">{{$schedule->activities}}</textarea>
                                </td>
                                <td class="border border-dark action">
                                    @if($key > 0)
                                        <input id="schedule{{$schedule->weekID}}" class="btn-danger schedule_remove" type="button" value="Del"/>
                                    @else
                                        <input id="addNewSchedule" type="button" value="Add"/>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="schedule1" class="schedule_row">
                            <td class="border border-dark short-col text-right">
                                <input type="text" name="schedule[1][week]" placeholder="Week" value="1"/>
                            </td>
                            <td class="border border-dark topic">
                                <textarea name="schedule[1][topic]" placeholder="Topic"></textarea>
                            </td>
                            <td class="border border-dark LO_col text-center">
                                <!-- LO -->
                            </td>
                            <td class="border border-dark">
                                <textarea name="schedule[1][readings]" placeholder="Readings"></textarea>
                            </td>
                            <td class="border border-dark">
                                <textarea name="schedule[1][activities]" placeholder="Activities"></textarea>
                            </td>
                            <td class="border border-dark action">
                                <input id="addNewSchedule" type="button" value="Add"/>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    {{--Appendix--}}
    <table class="vertical-table mx-auto mt-5 border border-dark">
        <tr><td class="border border-dark border-bottom-0 font-weight-bold" colspan="2">Appendix</td></tr>
        <tr>
            <td class="border border-dark border-top-0">
                <table id="appendix" class="w-100">
                    @if(isset($course) and !$course->appendix->isempty())
                        @foreach($course->appendix as $key=>$appendix)
                            <tr id="appendix{{$appendix->ID}}" class="appendix_row">
                                <td class="border border-dark border-bottom-0">
                                    <label for="appendix{{$appendix->ID}}Input">Appendix {{$appendix->ID}}:</label>
                                    <input id="appendix{{$appendix->ID}}Input" type="text" name="appendix[{{$appendix->ID}}][header]" placeholder="Appendix Header" value="{{$appendix->header}}"/>
                                </td>
                                <td class="border border-dark border-bottom-0 action">
                                    @if($key > 0)
                                        <input type='button' name='remove' id='appendix{{$appendix->ID}}' class='btn btn-danger appendix_remove' value='Del'>
                                    @else
                                        <input id="addNewAppendix" type="button" value="Add"/>
                                    @endif Appendix
                                </td>
                            </tr>
                            <tr id="appendix{{$appendix->ID}}Description" class="appendix_description_row">
                                <td class="border border-dark border-top-0">
                                    <label for="appendix{{$appendix->ID}}Textarea">Description:</label>
                                    <textarea id="appendix{{$appendix->ID}}Textarea" name="appendix[{{$appendix->ID}}][description]" placeholder="Description">{{$appendix->description}}</textarea>
                                </td>
                                <td class="border border-dark border-top-0 action">
                                     <input id="appendix{{$appendix->ID}}" class="addNewCriteria" type="button" value="Add" @if(!$appendix->criteria->isempty())disabled @endif/>Criteria Table
                                </td>
                            </tr>
                            @if(!$appendix->criteria->isempty())
                                <tr id="appendix{{$appendix->ID}}Criteria" class="appendix_criteria_row"><td colspan="2">
                                    <table class="w-100 border border-dark">
                                        <tr>
                                            <td class="w-25 border border-dark" rowspan="2">Criteria<br> for Appendix {{$appendix->ID}}'</td>
                                            <td class="w-75 border border-dark" colspan="3">Standards</td>
                                            <td class="border border-dark action" rowspan="2">
                                                <input id="appendix'+appendix_id+'" class="btn-danger criteria_remove" type="button" value="Del" />Criteria Table
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 border border-dark mid-col">Fail Standard<br>(0-39%)</td>
                                            <td class="w-25 border border-dark mid-col">Pass Standard<br>(40-80%)</td>
                                            <td class="w-25 border border-dark mid-col">High Standard<br>(81-100%)</td>
                                        </tr>
                                        @foreach($appendix->criteria as $cKey=>$criteria)
                                            <tr id="appendix{{$appendix->ID}}Criteria{{$criteria->ID}}" class="appendix'+appendix_id+'_criteria_row">
                                                <td class="w-25 border border-dark mid-col"><textarea name="appendix[{{$appendix->ID}}][criteria][{{$criteria->ID}}][header]" placeholder="Assessment">{{$criteria->header}}</textarea></td>
                                                <td class="w-25 border border-dark mid-col"><textarea name="appendix[{{$appendix->ID}}][criteria][{{$criteria->ID}}][fail]" placeholder="Fail Standards">{{$criteria->fail}}</textarea></td>
                                                <td class="w-25 border border-dark mid-col"><textarea name="appendix[{{$appendix->ID}}][criteria][{{$criteria->ID}}][pass]" placeholder="Pass Standards">{{$criteria->pass}}</textarea></td>
                                                <td class="w-25 border border-dark mid-col"><textarea name="appendix[{{$appendix->ID}}][criteria][{{$criteria->ID}}][high]" placeholder="High Standards">{{$criteria->high}}</textarea></td>
                                                <td class="border border-dark action">
                                                @if($cKey > 0)
                                                    <input id="appendix{{$appendix->ID}}Criteria{{$criteria->ID}}" class="btn-danger criteriaRow_remove" type="button" value="Del" />
                                                @else
                                                    <input id="appendix{{$appendix->ID}}" class="addNewCriteriaRow" type="button" value="Add" />
                                                @endif
                                                Criteria Row</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr id="appendix1" class="appendix_row">
                            <td class="border border-dark border-bottom-0">
                                <label for="appendix1Input">Appendix 1:</label>
                                <input id="appendix1Input" type="text" name="appendix[1][header]" placeholder="Appendix Header"  />
                            </td>
                            <td class="border border-dark border-bottom-0 action">
                                <input id="addNewAppendix" type="button" value="Add"/>Appendix
                            </td>
                        </tr>
                        <tr id="appendix1Description" class="appendix_description_row">
                            <td class="border border-dark border-top-0">
                                <label for="appendix1Textarea">Description:</label>
                                <textarea id="appendix1Textarea" name="appendix[1][description]" placeholder="Description"></textarea>
                            </td>
                            <td class="border border-dark border-top-0 action">
                                <input id="appendix1" class="addNewCriteria" type="button" value="Add"/>Criteria Table
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</form>
@endsection
