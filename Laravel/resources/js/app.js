/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});


$(document).ready(function(){
    let bCreate = false, bUpdate = false;
    if (window.location.pathname.split('/')[2] === 'create') bCreate = true;
    else if (window.location.pathname.split('/')[3] === 'edit') bUpdate = true;
    else if (window.location.pathname.split('/')[1] === 'search'){
        let cbChecked = [];
        $("#searchContactHours input:checkbox").click(function(){
            let ID = '';
            switch ($(this).prop('value')) {
                case 'lecture': ID = '#CHlec'; break;
                case 'tel': ID = '#CHtel'; break;
                case 'tutorial': ID = '#CHtut'; break;
                case 'lab': ID = '#CHlab'; break;
                case 'exampleclass': ID = '#CHexc'; break;
            }
            if($(this).is(":checked") && $("#searchContactHours input:checked").length < 4){
                $(ID).removeAttr("disabled");
                $(ID).focus();
                cbChecked.push(ID);
            } else {
                $(ID).attr("disabled", 'disabled');
                for(let i = 0; i < cbChecked.length; i++){
                    if(cbChecked[i] === ID){
                        cbChecked.splice(i, 1);
                    }
                }
            }
            if($("#searchContactHours input:checked").length > 3){
                $(this).prop('checked', false);
            }
        });

        let coll = document.getElementsByClassName("collapsible");
        let i;

        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                let nextRow = this.parentNode.parentNode.nextElementSibling;
                let nextNextRow = this.parentNode.parentNode.parentNode.childNodes[3].nextElementSibling;
                if (nextRow.style.display === "table-row" && nextNextRow.style.display === "table-row") {
                    nextRow.style.display = "none";
                    nextNextRow.style.display = "none";
                } else {
                    nextRow.style.display = "table-row";
                    nextNextRow.style.display = "table-row";
                }
            });
        }
    }

    //Common Function
    let grad_attr_count;
    let grad_attr_data;
    let grad_attr_id_arr = [];
    function getGradAttr(){
        jQuery.ajax({
            type:'GET',
            url:'/display/grad-attr',
            datatype:'json',
            data:{},
            // async:false,
            success: function(result){
                grad_attr_data = result;
                grad_attr_count = result.length;
                for (let i = 0; i < grad_attr_count; i++) grad_attr_id_arr.push(result[i]['ID']);

                if(bCreate){
                    assessment_DisplayAll_GradAttr_CheckBox();
                    map_GradAttr_Init();
                }
                else if (bUpdate){
                    if($('#assessment1 .gradAttrCol')[0].childElementCount === 0) assessment_DisplayAll_GradAttr_CheckBox();
                    if($('.mapLOGradAttr').length === 0) map_GradAttr_Init();
                    map_CalculatePercentage();
                }
            }
        });
    }
    function addNew_GradAttr_Cell(SN, section, full){
        let gradAttrCheckbox = '<span class="text-primary">Hover mouse over text for more info.</span><br/>';
        for (let j = 0; j < grad_attr_data.length; j++){
            let checkBoxID = "";
            if(!full) checkBoxID = 'id="'+SN+grad_attr_data[j]['ID']+'"';
            gradAttrCheckbox += '<label class="cbGradAttrLbl">' +
                '<input '+checkBoxID+' type="checkbox" class="cbGradAttr" name="'+section+'['+SN+'][gradAttr][]" value="'+grad_attr_data[j]['ID']+'"/>&nbsp;&nbsp;' +
                '<span title="'+grad_attr_data[j]['main']+'&#13;&#10;'+grad_attr_data[j]['description']+'">';
            if(full)
                gradAttrCheckbox += '('+grad_attr_data[j]['ID']+')&emsp;'+grad_attr_data[j]['main']+'</span></label><br>';
            else
                gradAttrCheckbox += '('+grad_attr_data[j]['ID']+')&emsp;&emsp;'+'</span></label>';
        }
        return gradAttrCheckbox;
    }
    function newLO_Checkbox(SN, section, LO_index){
        let section_index = section[0];
        let LO_Checkbox = '<label><input id="'+section_index+SN+'LO'+LO_index+'" type="checkbox" name="'+section+'['+SN+'][LO][]" value="'+LO_index+'"/>&emsp;'+LO_index+'<br></label>';
        $('#'+section+SN+' .LO_col').append(LO_Checkbox);
    }
    function delLO_Checkbox(SN, section, LO_index){
        $('#'+section[0]+SN+'LO'+LO_index).parent().remove();
    }
    function addNew_LO_Cell(SN, section){
        for (let c = 1; c <= $('#LOs tr').length; c++) newLO_Checkbox(SN, section, c);
    }
    function updateLO_Checkbox(section){
        for (let i = 1; i <= $('#'+section+' .'+section+'_row').length; i++){
            let LO_col_input = $('#'+section+i.toString()+' .LO_col input:checkbox');
            let LO_rows = $('#LOs tr');
            for (let j = LO_col_input.length; j < LO_rows.length; j++){
                newLO_Checkbox(i, section, j+1);
            }
            for (let j = LO_col_input.length; j > LO_rows.length; j--){
                delLO_Checkbox(i, section, j);
            }
        }
    }

    //Main Details
    let listDisabled = [];
    $('.contactType').change(function(){
        let select_index = $(this).attr('id').split('_')[2];

        if(this.selectedIndex !== 0)
            listDisabled[select_index] = this.selectedIndex;
        else
            delete listDisabled[select_index];
        $('.contactType option').each(function(){
            $(this).attr("hidden", false);
            for(let i = 0; i < listDisabled.length; i++){
                if(listDisabled[i] === this.index) $(this).attr("hidden", true);
            }ap
        });
    });

    //Learning Outcomes
    function newLORow(ID){
        return '<tr id="LO'+ID+'" class="LO_row">' +
            '<td class="border border-dark"><textarea id="iLO'+ID+'" name="objectives[LO]['+ID+'][description]" placeholder="Learning Outcomes"></textarea></td>' +
            '<td class="border border-dark action"><input id="LO'+ID+'" class="btn-danger LO_remove" type="button" name="remove" value="Del" /></td>' +
            '</tr>';
    }
    function learningOutcomes_Init(){
        for (let c = $('#LOs tr').length; c < 4; c++){
            $('#LOs').append(newLORow(c+1));
        }
    }
    $('#addNewLO').click(function(){
        $('#LOs').append(newLORow($('#LOs tr').length + 1));
        updateLO_Checkbox('assessment');
        map_Update_NewMappingRow();
        updateLO_Checkbox('schedule');
    });
    $(document).on('click', '.LO_remove', function(){
        let button_id = $(this).attr("id");
        $('#'+button_id).remove();

        // change all rows ID in order
        let LO_rows = document.getElementsByClassName('LO_row');
        for (let i = 1; i < LO_rows.length; i++){
            LO_rows[i].id = 'LO' + (i + 1);
            let LO_row_textarea = LO_rows[i].getElementsByClassName('loTextArea');
            LO_row_textarea[0].id = 'iLO' + (i + 1);
            let LO_row_buttons = LO_rows[i].getElementsByClassName('LO_remove');
            LO_row_buttons[0].id = 'LO' + (i + 1);
        }

        //Assessment
        updateLO_Checkbox('assessment');

        // MapGradAttr
        // Remove all MapGradAttr rows
        // Append new rows
        // Update MapGradAttr LO if exist
        let numMapLoGradAttr = $('#mappingGradAttr tr').length;
        for (let j = 1; j <= numMapLoGradAttr - 10; j++) $('#rowMapLO'+j).remove();
        map_LO_Init(); map_GradAttr_Init();
        map_Update_MapLO();

        // Schedule
        updateLO_Checkbox('schedule');
    });

    //Contents
    function newContentRow(SN){
        return '<tr id="topic'+SN+'" class="content_topic">'+
            '<td class="border border-dark short-col"><input type="text" name="content[topic]['+SN+'][ID]" placeholder="S/N" value="'+SN+'"/></td>'+
            '<td class="border border-dark"><textarea class="description" name="content[topic]['+SN+'][topic]" placeholder="Topic description"></textarea></td>'+
            '<td class="border border-dark att1 "><textarea name="content[topic]['+SN+'][details1]" placeholder="Topic details"></textarea></td>'+
            '<td class="border border-dark att2"><textarea name="content[topic]['+SN+'][details2]" placeholder="Topic details"></textarea>'+
            '<label><input type="checkbox" id="topic'+SN+'" class="attDetailMerge" name="content[merge][]" value="'+SN+'"/> Merge bottom</label></td>'+
            '<td class="border border-dark"><input id="topic'+SN+'" class="btn-danger content_remove" type="button" value="Del" /></td>';
    }
    function content_Init(){
        for (let c = $('#contents tr').length; c <= 4; c++){
            $('#contents').append(newContentRow(c));
        }
    }
    function content_Reindex(){
        let content_topics = document.getElementsByClassName('content_topic');
        let first_index = parseInt(content_topics[0].children[0].children[0].value, 10);
        for (let i = 0; i < content_topics.length; i++){
            content_topics[i].id = 'topic'+(first_index + i).toString();
            content_topics[i].children[0].children[0].value = i + first_index;
            content_topics[i].children[4].children[0].id = 'topic'+(i+first_index).toString();
        }
    }
    $('#addNewTopic').click(function(){
        let newSN = parseInt($('#SN').prop('value'),10) + $('#contents .content_topic').length;
        $('#contents').append(newContentRow(newSN));
    });
    $(document).on('click', '.content_remove', function(){
        let delete_row = $(this).attr('ID');
        let regexStr = delete_row.match(/[a-z]+|[^a-z]+/gi);
        $('#'+delete_row+'').remove();
        let previous_row = parseInt(regexStr[1],10) - 1;
        let previous_row_merge = $('#topic'+previous_row+' .att2 .attDetailMerge');
        if (previous_row_merge.prop('checked') === true){
            previous_row_merge.prop('checked', false);
            $('#topic'+previous_row+' .att2').removeAttr('rowspan');
        }
        else {
            let next_row = parseInt(regexStr[1],10) + 1;
            $('#topics'+next_row+' .att2').removeAttr('style');
        }

        // Re-number contents SN & ID
        content_Reindex();
    });
    $(document).on('click', '.attDetailMerge', function(){
        let topic_row = parseInt($(this).attr("id").match(/[a-z]+|[^a-z]+/gi)[1], 10);
        let merge_row = topic_row + 1;
        if ($(this).prop('checked')===true){
            $('#topic'+topic_row+' .att2').attr('rowspan', 2);
            $('#topic'+merge_row+' .att2').attr('style', 'display:none');
        }
        else{
            $('#topic'+topic_row+' .att2').removeAttr('rowspan');
            $('#topic'+merge_row+' .att2').removeAttr('style');
        }
    });
    $(document).on('change', '#contents', content_Reindex);

    //Assessment
    function newAssessmentRow(SN){
        return '<tr id="assessment'+SN+'" class="assessment_row">'+
            '<td class="border border-dark"><textarea name="assessment['+SN+'][title]" placeholder="Component"></textarea></td>'+
            '<td class="border border-dark text-center LO_col"></td>'+
            '<td class="border border-dark pl-3 gradAttrCol"></td>'+
            '<td class="border border-dark"><input type="text" id="w'+SN+'" class="assessmentWeight" name="assessment['+SN+'][weight]" placeholder="Percentage"/></td>'+
            '<td class="border border-dark pl-3">'+
            '<input id="assessment'+SN+'Individual" type="radio" name="assessment['+SN+'][category]" value="individual" checked/>' +
            '<label for="assessment'+SN+'Individual">Individual</label><br>' +
            '<input id="assessment'+SN+'Team" type="radio" name="assessment['+SN+'][category]" value="team"/>' +
            '<label for="assessment'+SN+'Team">Team</label>'+
            '</td>'+
            '<td class="border border-dark"><textarea name="assessment['+SN+'][rubrics]" placeholder="Rubrics info to be appended at the end of the document"></textarea></td>'+
            '<td class="border border-dark"><input id="assessment'+SN+'" class="btn-danger assessment_remove" type="button"  value="Del"></td>'+
            '</tr>';
    }
    function assessment_Add_LO_CheckBox(SN){
        addNew_LO_Cell(SN, 'assessment');
    }
    function assessment_Add_GradAttr_CheckBox(SN){
        $('#assessment'+SN+' .gradAttrCol').append(addNew_GradAttr_Cell(SN, 'assessment', true));
    }
    function assessment_DisplayALL_LO_CheckBox(){
        for (let i = 1; i <= $('#assessment tr').length - 1; i++) assessment_Add_LO_CheckBox(i);
    }
    function assessment_DisplayAll_GradAttr_CheckBox(){
        for (let i = 1; i <= $('#assessment tr').length - 1; i++){
            assessment_Add_GradAttr_CheckBox(i);
        }
    }
    function assessment_Create(){
        for (let id = $('#assessment tr').length; id <= 2; id++) $('#assessment').append(newAssessmentRow(id));
        assessment_DisplayALL_LO_CheckBox();
    }
    function assessment_Reindex(){
        // console.log('assessment_Reindex');
        let assessment_rows = document.getElementsByClassName('assessment_row');
        for (let i = 1; i < assessment_rows.length; i++){
            assessment_rows[i].id = 'assessment'+(i+1).toString();
            let lo_tested_elements = assessment_rows[i].children[1].children;
            for (let j = 0; j < lo_tested_elements.length; j+=2){
                lo_tested_elements[j].children[0].name = 'assessment[LO]['+(i+1)+'][]';
                lo_tested_elements[j].children[0].id = 'a'+(i+1).toString()+'LO'+(j+1);
            }
            let grad_attr_elements = assessment_rows[i].children[2].children;
            for (let j = 2; j < grad_attr_elements.length; j+=2){
                grad_attr_elements[j].children[0].name = 'assessment[gradAttr]['+(i+1)+'][]';
            }
        }
    }
    $('#addNewAssessment').click(function(){
        let numAssessment = $('#assessment tr').length - 1;
        $('#assessment'+numAssessment).after(newAssessmentRow(numAssessment+1));
        assessment_Add_LO_CheckBox(numAssessment+1);
        assessment_Add_GradAttr_CheckBox(numAssessment+1);
    });
    $(document).on('click', '.assessment_remove', function(){
        $('#'+$(this).attr("id")).remove();
        assessment_Reindex();
    });

    //MapGradAttr
    function newMapLORow(SN){
        return '<tr id="rowMapLO'+SN+'" class="LO_gradAttr">' +
            '<td id="mapLO'+SN+'" class="long-col border border-dark"></td>';
    }
    function newMapGradAttrRow(result, SN){
        return '<td class="border border-dark mapLOGradAttr" colspan="'+(grad_attr_count+1)+'">' +
            addNew_GradAttr_Cell(SN, 'mappingLO', false) +
            '</td>' +
            '<td colspan="2" class="short-col border border-dark"></td></tr>'
    }
    function map_LO_Init(){
        for(let c = 1; c <= $('#LOs tr').length; c++){
            $('#mapGradAttrLegend').before(newMapLORow(c));
        }
    }
    function map_GradAttr_Init(){
        for (let c = 1; c <= $('#LOs tr').length; c++){
            $('#mapLO'+c).after(newMapGradAttrRow(grad_attr_data, c));
        }
    }
    function map_Update_CourseLabel(){
        let courseRep = '';
        let CE_checked = document.getElementById('course_CE').checked;
        let CZ_checked = document.getElementById('course_CZ').checked;

        switch(true){
            case CE_checked && CZ_checked:
                courseRep = 'CE/CZ';
                document.getElementById('rep_CE').innerHTML = "<img src='/assets/full-dot.png' style='width:17px' alt=''>";
                document.getElementById('rep_CZ').innerHTML = "<img src='/assets/full-dot.png' style='width:17px' alt=''>";
                break;
            case CE_checked:
                courseRep = 'CE';
                document.getElementById('rep_CE').innerHTML = "<img src='/assets/full-dot.png' style='width:17px' alt=''>";
                document.getElementById('rep_CZ').innerHTML = "";
                break;
            case CZ_checked:
                courseRep = 'CZ';
                document.getElementById('rep_CE').innerHTML = "";
                document.getElementById('rep_CZ').innerHTML = "<img src='/assets/full-dot.png' style='width:17px' alt=''>";
                break;
            default:
                courseRep = '';
        }
        let courseCode = document.getElementById("course_code").value;
        let courseTitle = document.getElementById("course_title").value;
        document.getElementById("courseCodeTitle").innerHTML = courseRep+courseCode+' '+courseTitle;
    }
    function map_Update_NewMappingRow(){
        let numLO = $('#LOs tr').length;
        let numMapLORow = $('#mappingGradAttr tr').length - 10;

        if(numLO <= numMapLORow) return;

        for(numMapLORow; numMapLORow < numLO; numMapLORow++){
            $('#mapGradAttrLegend').before(newMapLORow(numLO));
            $('#mapLO'+numLO).after(newMapGradAttrRow(grad_attr_data, numLO));
        }
    }
    function map_Update_MapLO(){
        for(let c = 1; c <= $('#LOs tr').length; c++){
            document.getElementById("mapLO"+c).innerHTML = document.getElementById("iLO"+c).value;
        }
    }
    function map_CalculatePercentage(){
        let numLO = $('#LOs tr').length;
        // Init array to count
        let grad_attr_count_dict = {};
        for (let i = 0; i < grad_attr_data.length; i++){
            grad_attr_count_dict[grad_attr_data[i]['ID']] = 0;
        }
        // Start counting
        for(let i = 1; i <= numLO; i++){
            for(let ga = 0; ga < grad_attr_id_arr.length; ga++){
                if(document.getElementById(i+grad_attr_id_arr[ga]).checked === true) {
                    grad_attr_count_dict[grad_attr_id_arr[ga]]++;
                }
            }
        }
        for (let ga = 0; ga < grad_attr_id_arr.length; ga++){
            let value = grad_attr_count_dict[grad_attr_id_arr[ga]]/numLO;
            switch(true){
                case (value >= 0.75):
                    document.getElementById("grad_attr_"+grad_attr_id_arr[ga]).src = '/assets/full-dot.png';
                    break;
                case (value >= 0.5):
                    document.getElementById("grad_attr_"+grad_attr_id_arr[ga]).src = '/assets/half-dot.png';
                    break;
                case (value >= 0.25):
                    document.getElementById("grad_attr_"+grad_attr_id_arr[ga]).src = '/assets/empty-dot.png';
                    break;
                default:
                    document.getElementById("grad_attr_"+grad_attr_id_arr[ga]).src = '/assets/blank-dot.png';
                    break;
            }
        }
    }
    $(document).on('change', '.course_rep', function(){
        let checkbox = $(this).attr('id');
        if(checkbox === 'course_CE' &&
            document.getElementById('course_CE').checked === false &&
            document.getElementById('course_CZ').checked === false){
            document.getElementById('course_CZ').checked = 'checked';
        }
        else if (checkbox === 'course_CZ' &&
            document.getElementById('course_CZ').checked === false &&
            document.getElementById('course_CE').checked === false){
            document.getElementById('course_CE').checked = 'checked';
        }
        map_Update_CourseLabel();
    });
    $(document).on('change', '#course_aims', function(){
        document.getElementById("mapping_overall_statement").innerHTML = document.getElementById("course_aims").value;
    });
    $(document).on('change', '#course_code', map_Update_CourseLabel);
    $(document).on('change', '#course_title', map_Update_CourseLabel);
    $(document).on('change', '#LOs', map_Update_MapLO);
    $(document).on('change', '#mappingGradAttr', map_CalculatePercentage);

    //Approach
    function newApproachRow(SN){
        return '<tr id="approach'+SN+'" class="approach_row">' +
            '<td class="border border-dark"><textarea name="approach['+SN+'][header]" placeholder="Approach Header"></textarea></td>' +
            '<td class="border border-dark"><textarea name="approach['+SN+'][description]" placeholder="Approach Description"></textarea></td>' +
            '<td class="border border-dark action"><input id="approach'+SN+'" class="btn-danger approach_remove" type="button" value="Del"/></td>' +
            '</tr>';
    }
    $('#addNewApproach').click(function(){
       let numApproach = $('#approach tr').length - 1;
       $('#approach').append(newApproachRow(numApproach + 1));
    });
    $(document).on('click', '.approach_remove', function(){
        $('#'+$(this).attr("id")).remove();

        let approach_rows = document.getElementsByClassName('approach_row');
        for (let i = 1; i < approach_rows.length; i++){
            approach_rows[i].id = 'approach'+(i+1).toString();
            approach_rows[i].children[2].children[0].id = 'approach'+(i+1).toString();
        }
    });

    //Reference
    function newReferenceRow(SN){
        return '<tr id="reference'+SN+'" class="reference_row">' +
            '<td class="border border-dark"><textarea name="reference[]" placeholder="References Details"></textarea></td>' +
            '<td class="border border-dark action"><input id="reference'+SN+'" class="btn-danger reference_remove" type="button" value="Del"/></td>' +
            '</tr>';
    }
    $('#addNewReference').click(function(){
        let num_reference_row = $('#reference tr').length;
        $('#reference').append(newReferenceRow(num_reference_row+1));
    });
    $(document).on('click', '.reference_remove', function(){
        $('#'+$(this).attr('id')).remove();

        let reference_rows = document.getElementsByClassName('reference_row');
        for (let i = 1; i < reference_rows.length; i++){
            reference_rows[i].id = 'reference'+(i+1).toString();
            reference_rows[i].children[1].children[0].id = 'reference'+(i+1).toString();
        }
    });

    //Instructor
    function newInstructorRow(SN){
        return '<tr id="instructor'+SN+'" class="instructor_row">' +
            '<td class="border border-dark w-25"><input type="text" name="instructor['+SN+'][name]" placeholder="Name" /></td>' +
            '<td class="border border-dark w-25"><input type="text" name="instructor['+SN+'][office]" placeholder="Office Location" /></td></td>' +
            '<td class="border border-dark w-25"><input type="text" name="instructor['+SN+'][phone]" placeholder="Phone" /></td></td>' +
            '<td class="border border-dark w-25"><input type="text" name="instructor['+SN+'][email]" placeholder="Email" /></td></td>' +
            '<td class="border border-dark action"><input id="instructor'+SN+'" class="btn-danger instructor_remove" type="button" value="Del"/></td>';
    }
    $('#addNewInstructor').click(function(){
       let num_instructor_row = $('#instructor tr').length;
       $('#instructor').append(newInstructorRow(num_instructor_row));
    });
    $(document).on('click', '.instructor_remove', function(){
        $('#'+$(this).attr('id')).remove();

        let instructor_rows = document.getElementsByClassName('instructor_row');
        for (let i = 1; i < instructor_rows.length; i++){
            instructor_rows[i].id = 'instructor'+(i+1).toString();
            instructor_rows[i].children[4].children[0].id = 'instructor'+(i+1).toString();
        }
    });

    //Schedule
    function newScheduleRow(SN){
        return '<tr id="schedule'+SN+'" class="schedule_row">' +
            '<td class="border border-dark short-col"><input type="text" name="schedule[week][]" placeholder="Week" value="'+SN+'"/></td>' +
            '<td class="border border-dark topic"><textarea name="schedule[topic][]" placeholder="Topic"></textarea></td>' +
            '<td class="border border-dark mid-col LO_col text-center"></td>' +
            '<td class="border border-dark mid-col"><textarea name="schedule[readings][]" placeholder="Readings"></textarea></td>' +
            '<td class="border border-dark med-col"><textarea name="schedule[activities][]" placeholder="Activities"></textarea></td>' +
            '<td class="border border-dark action"><input id="schedule'+SN+'" class="btn-danger schedule_remove" type="button" value="Del"/></td>';
    }
    function schedule_Add_LO_CheckBox(SN){
        addNew_LO_Cell(SN, 'schedule');
    }
    function schedule_DisplayAll_LO_CheckBox(){
        let numScheduleRow = $('#schedule tr').length - 1;
        for (let i = 1; i <= numScheduleRow; i++){
          schedule_Add_LO_CheckBox(i);
        }
    }
    function schedule_Init(){
        let numScheduleRow = $('#schedule tr').length - 1;
        for (let i = numScheduleRow; i < 4; i++){
            $('#schedule').append(newScheduleRow(i+1));
        }
        schedule_DisplayAll_LO_CheckBox();
    }
    function schedule_Reindex(){
        let schedule_rows = document.getElementsByClassName('schedule_row');
        for (let i = 1; i < schedule_rows.length; i++){
            schedule_rows[i].id = 'schedule'+(i+1).toString();
            schedule_rows[i].children[0].children[0].value = (i+1);
            schedule_rows[i].children[2].children[0].children[0].name = 'schedule['+(i+1)+'][LO][]';
            schedule_rows[i].children[5].children[0].id = 'schedule'+(i+1).toString();
        }
    }
    $('#addNewSchedule').click(function(){
        let numScheduleRow = $('#schedule tr').length - 1;
        $('#schedule').append(newScheduleRow(numScheduleRow+1));
        schedule_Add_LO_CheckBox(numScheduleRow+1);
    });
    $(document).on('click', '.schedule_remove', function(){
        $('#'+$(this).attr("id")).remove();
        schedule_Reindex();
    });

    //appendix
    function newAppendixRow(SN){
        return '<tr id="appendix'+SN+'" class="appendix_row">' +
            '<td class="border border-dark border-bottom-0">' +
            '<label for="appendix'+SN+'Input">Appendix '+SN+':</label><input id="appendix'+SN+'Input" type="text" name="appendix['+SN+'][header]" placeholder="Appendix Header"/></td>' +
            '<td class="border border-dark border-bottom-0 action"><input id="appendix'+SN+'" class="btn-danger appendix_remove" type="button" value="Del"/>Appendix</td><tr>' +
            '<tr id="appendix'+SN+'Description"" class="appendix_description_row">' +
            '<td class="border border-dark border-top-0">' +
            '<label for="appendix'+SN+'Textarea">Description:</label><textarea id="appendix'+SN+'Textarea" name="appendix['+SN+'][description]" placeholder="Description"></textarea></td>' +
            '<td class="border border-dark border-top-0 action"><input id="appendix'+SN+'" class="addNewCriteria" type="button" value="Add"/>Criteria Table</td>' +
            '</tr>';
    }
    function newCriteriaTable(appendix_id){
        return '<tr id="appendix'+appendix_id+'Criteria" class="appendix_criteria_row"><td colspan="2">' +
            '<table class="w-100 border border-dark">' +
            '<tr>' +
            '<td class="w-25 border border-dark" rowspan="2">Criteria\r\nfor Appendix '+appendix_id+'</td>' +
            '<td class="w-75 border border-dark" colspan="3">Standards</td>' +
            '<td class="border border-dark action" rowspan="2"><input id="appendix'+appendix_id+'" class="btn-danger criteria_remove" type="button" value="Del" />Criteria Table</td>' +
            '</tr><tr>' +
            '<td class="w-25 border border-dark mid-col">Fail Standard\r\n(0-39%)</td>' +
            '<td class="w-25 border border-dark mid-col">Pass Standard\r\n(40-80%)</td>' +
            '<td class="w-25 border border-dark mid-col">High Standard\r\n(81-100%)</td>' +
            '</tr><tr id="appendix'+appendix_id+'Criteria1" class="appendix'+appendix_id+'_criteria_row">' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria][1][header]" placeholder="Assessment"></textarea></td>' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria][1][fail]" placeholder="Fail Standards"></textarea></td>' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria][1][pass]" placeholder="Pass Standards"></textarea></td>' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria][1][high]" placeholder="High Standards"></textarea></td>' +
            '<td class="border border-dark action"><input id="appendix'+appendix_id+'" class="addNewCriteriaRow" type="button" value="Add" />Criteria Row</td>' +
            '</tr>' +
            '</table>' +
            '</td></tr>';
    }
    function newCriteriaRow(appendix_id, SN){
        return '<tr id="appendix'+appendix_id+'Criteria'+SN+'" class="appendix'+appendix_id+'_criteria_row">' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria]['+SN+'][header]" placeholder="Assessment"></textarea></td>' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria]['+SN+'][fail]" placeholder="Fail Standards"></textarea></td>' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria]['+SN+'][pass]" placeholder="Pass Standards"></textarea></td>' +
            '<td class="w-25 border border-dark mid-col"><textarea name="appendix['+appendix_id+'][criteria]['+SN+'][high]" placeholder="High Standards"></textarea></td>' +
            '<td class="border border-dark action"><input id="appendix'+appendix_id+'Criteria'+SN+'" class="btn-danger criteriaRow_remove" type="button" value="Del" />Criteria Row</td>' +
            '</tr>';
    }
    $('#addNewAppendix').click(function(){
        let numAppendixRow = document.getElementsByClassName('appendix_row').length;
        $('#appendix').append(newAppendixRow(numAppendixRow+1));
    });
    $(document).on('click', '.appendix_remove', function(){
        $('#'+$(this).attr("id")).remove();
        $('#'+$(this).attr("id")+'Description').remove();
        $('#'+$(this).attr("id")+'Criteria').remove();

        let appendix_rows = document.getElementsByClassName('appendix_row');
        let appendix_description_rows = document.getElementsByClassName('appendix_description_row');
        for (let i = 1; i < appendix_rows.length; i++){
            appendix_rows[i].id = 'appendix'+(i+1).toString();
            appendix_rows[i].children[0].children[0].innerHTML = 'Appendix '+(i+1).toString()+':';
            appendix_rows[i].children[1].children[0].id = 'appendix'+(i+1).toString();
        }
        for (let i = 1; i < appendix_description_rows.length; i++){
            appendix_description_rows[i].id = 'appendix'+(i+1).toString()+'Description';
            appendix_description_rows[i].children[1].children[0].id = 'appendix'+(i+1).toString();
        }
    });
    $(document).on('click', '.addNewCriteria', function(){
        let appendix_id = $(this).attr("id").match(/[a-z]+|[^a-z]+/gi)[1];
        if(document.getElementById('appendix'+appendix_id+'Criteria') != null) return;
        $('#appendix'+appendix_id+'Description').after(newCriteriaTable(appendix_id));
        $(this).prop('disabled', true);
    });
    $(document).on('click', '.criteria_remove', function(){
        $('#'+$(this).attr("id")+'Criteria').remove();
        $('#'+$(this).attr("id")+'.addNewCriteria').prop('disabled', false);
    });
    $(document).on('click', '.addNewCriteriaRow', function(){
        let appendix_id = $(this).attr("id").match(/[a-z]+|[^a-z]+/gi)[1];
        let numCriteriaRow = document.getElementsByClassName('appendix'+appendix_id+'_criteria_row').length;
        $('#appendix'+appendix_id+'Criteria'+numCriteriaRow).after(newCriteriaRow(appendix_id, numCriteriaRow+1));
    });
    $(document).on('click', '.criteriaRow_remove', function(){
        let ids = $(this).attr("id").match(/[a-z]+|[^a-z]+/gi);
        let appendix_id = ids[1];
        let criteria_id = ids[3];
        $('#appendix'+appendix_id+'Criteria'+criteria_id).remove();

        let criteria_rows = document.getElementsByClassName('appendix'+appendix_id+'_criteria_row');
        for (let i = 1; i < criteria_rows.length; i++){
            criteria_rows[i].id = 'appendix'+(appendix_id).toString()+'Criteria'+(i+1).toString();
            criteria_rows[i].children[4].children[0].id = 'appendix'+(appendix_id).toString()+'Criteria'+(i+1).toString();
        }
    });

    getGradAttr();
    if (bCreate){
        learningOutcomes_Init();
        content_Init();
        assessment_Create();
        map_LO_Init(); map_Update_CourseLabel();
        schedule_Init();
    }
    else if (bUpdate){
        if($('#assessment1 .LO_col')[0].childElementCount === 0) assessment_DisplayALL_LO_CheckBox();
        if($('.LO_gradAttr').length === 0) map_LO_Init();
        if($('#schedule1 .LO_col')[0].childElementCount === 0) schedule_DisplayAll_LO_CheckBox();
    }
});
