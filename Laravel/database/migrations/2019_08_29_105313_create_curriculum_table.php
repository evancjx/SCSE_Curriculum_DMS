<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurriculumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::connection('mysql_curriculum')->dropIfExists('criteria');
//        Schema::connection('mysql_curriculum')->dropIfExists('appendix');
//        Schema::connection('mysql_curriculum')->dropIfExists('schedule_lo');
//        Schema::connection('mysql_curriculum')->dropIfExists('schedule');
//        Schema::connection('mysql_curriculum')->dropIfExists('common');
//        Schema::connection('mysql_curriculum')->dropIfExists('formativeFeedback');
//        Schema::connection('mysql_curriculum')->dropIfExists('reference');
//        Schema::connection('mysql_curriculum')->dropIfExists('approach');
//        Schema::connection('mysql_curriculum')->dropIfExists('instructor');
//        Schema::connection('mysql_curriculum')->dropIfExists('author');
//        Schema::connection('mysql_curriculum')->dropIfExists('academicStaff');
//        Schema::connection('mysql_curriculum')->dropIfExists('gradAttr_percent');
//        Schema::connection('mysql_curriculum')->dropIfExists('lo_gradAttr');
//        Schema::connection('mysql_curriculum')->dropIfExists('rubrics');
//        Schema::connection('mysql_curriculum')->dropIfExists('assessment_gradAttr');
//        Schema::connection('mysql_curriculum')->dropIfExists('assessment_LO');
//        Schema::connection('mysql_curriculum')->dropIfExists('assessment_category');
//        Schema::connection('mysql_curriculum')->dropIfExists('assessment');
//        Schema::connection('mysql_curriculum')->dropIfExists('contentAttDetails');
//        Schema::connection('mysql_curriculum')->dropIfExists('contentAtt');
//        Schema::connection('mysql_curriculum')->dropIfExists('content');
//        Schema::connection('mysql_curriculum')->dropIfExists('graduateAttributes');
//        Schema::connection('mysql_curriculum')->dropIfExists('learningOutcomes');
//        Schema::connection('mysql_curriculum')->dropIfExists('objectives');
//        Schema::connection('mysql_curriculum')->dropIfExists('contactHour');
//        Schema::connection('mysql_curriculum')->dropIfExists('prerequisite');
//        Schema::connection('mysql_curriculum')->dropIfExists('course');

        Schema::connection('mysql_curriculum')->create('course', function (Blueprint $table){
            $table->string('rep');
            $table->string('code');
            $table->string('title');
            $table->tinyInteger('noAU');
            $table->string('category');
            $table->string('proposalDate')->nullable();

            $table->primary(['code']);
        });
        Schema::connection('mysql_curriculum')->create('prerequisite', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign('course_code')
                ->references('code')->on('course')
                ->onDelete('cascade');
            $table->string('prerequisiteCode');
            $table->primary(['course_code', 'prerequisiteCode']);
        });
        Schema::connection('mysql_curriculum')->create('contactHour', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign('course_code')
                ->references('code')->on('course')
                ->onDelete('cascade');
            $table->tinyInteger('lecture')->nullable();
            $table->tinyInteger('tel')->nullable();
            $table->tinyInteger('tutorial')->nullable();
            $table->tinyInteger('lab')->nullable();
            $table->tinyInteger('exampleClass')->nullable();

            $table->primary('course_code');
        });
        Schema::connection('mysql_curriculum')->create('objectives', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign('course_code')
                ->references('code')->on('course')
                ->onDelete('cascade');
            $table->text('courseAims');

            $table->primary(['course_code']);
        });
        Schema::connection('mysql_curriculum')->create('learningOutcomes', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign('course_code')
                ->references('code')->on('course')
                ->onDelete('cascade');
            $table->tinyInteger('ID');
            $table->text('description');

            $table->primary(['course_code', 'ID']);
        });
        Schema::connection('mysql_curriculum')->create('graduateAttributes', function (Blueprint $table){
            $table->string('ID', 3);
            $table->string('main', 100);
            $table->text('description');

            $table->primary(['ID']);
        });

        Schema::connection('mysql_curriculum')->create('lo_gradAttr', function (Blueprint $table){
            $table->string('course_code');
            $table->tinyInteger('lo_ID');
            $table->foreign(['course_code', 'lo_ID'])
                ->references(['course_code', 'ID'])->on('learningOutcomes')
                ->onDelete('cascade');

            $table->string('gradAttrID', 3);
            $table->foreign(['gradAttrID'])
                ->references(['ID'])->on('graduateAttributes');

            $table->primary(['course_code', 'lo_ID', 'gradAttrID']);
        });
        Schema::connection('mysql_curriculum')->create('content', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->tinyInteger('ID');
            $table->text('topics');

            $table->primary(['course_code', 'ID']);
        });
        Schema::connection('mysql_curriculum')->create('contentAtt', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->string('att1');
            $table->string('att2');

            $table->primary(['course_code']);
        });
        Schema::connection('mysql_curriculum')->create('contentAttDetails', function (Blueprint $table){
            $table->string('course_code');
            $table->tinyInteger('content_ID');
            $table->foreign(['course_code', 'content_ID'])
                ->references(['course_code', 'ID'])->on('content')
                ->onDelete('cascade');

            $table->string('details1');
            $table->string('details2');
            $table->tinyInteger('rowspan');

            $table->primary(['course_code', 'content_ID']);
        });
        Schema::connection('mysql_curriculum')->create('assessment', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign('course_code')
                ->references('code')->on('course')
                ->onDelete('cascade');
            $table->string('ID', 3);
            $table->string('component', 100);
            $table->tinyInteger('weightage');

            $table->primary(['course_code', 'ID']);
        });
        Schema::connection('mysql_curriculum')->create('assessment_category', function (Blueprint $table){
            $table->string('course_code');
            $table->string('assessment_ID', 3);
            $table->foreign(['course_code', 'assessment_ID'])
                ->references(['course_code', 'ID'])->on('assessment')
                ->onDelete('cascade');

            $table->string('category', 255);

            $table->primary(['course_code', 'assessment_ID']);
        });
        Schema::connection('mysql_curriculum')->create('assessment_LO', function (Blueprint $table){
            $table->string('course_code');
            $table->string('assessment_ID', 3);
            $table->foreign(['course_code', 'assessment_ID'])
                ->references(['course_code', 'ID'])->on('assessment')
                ->onDelete('cascade');

            $table->tinyInteger('lo_ID');
            $table->foreign(['course_code', 'lo_ID'])
                ->references(['course_code', 'ID'])->on('learningOutcomes')
                ->onDelete('cascade');

            $table->primary(['course_code', 'assessment_ID', 'lo_ID']);
        });
        Schema::connection('mysql_curriculum')->create('assessment_gradAttr', function (Blueprint $table){
            $table->string('course_code');

            $table->string('assessment_ID', 3);
            $table->foreign(['course_code', 'assessment_ID'])
                ->references(['course_code', 'ID'])->on('assessment')
                ->onDelete('cascade');

            $table->string('gradAttrID', 255);
            $table->foreign(['gradAttrID'])
                ->references(['ID'])->on('graduateAttributes');

            $table->primary(['course_code', 'assessment_ID', 'gradAttrID']);
        });
        Schema::connection('mysql_curriculum')->create('rubrics', function (Blueprint $table){
            $table->string('course_code');
            $table->string('assessment_ID', 3);
            $table->foreign(['course_code', 'assessment_ID'])
                ->references(['course_code', 'ID'])->on('assessment')
                ->onDelete('cascade');
            $table->text('description');

            $table->primary(['course_code', 'assessment_ID']);
        });

        Schema::connection('mysql_curriculum')->create('gradAttr_percent', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->string('gradAttrID', 3);
            $table->foreign(['gradAttrID'])
                ->references(['ID'])->on('graduateAttributes');

            $table->tinyInteger('percentage');

            $table->primary(['course_code', 'gradAttrID']);
        });
        Schema::connection('mysql_curriculum')->create('academicStaff', function (Blueprint $table){
            $table->integer('ID')->autoIncrement();
            $table->string('name');
            $table->string('office')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
        });
        Schema::connection('mysql_curriculum')->create('author', function (Blueprint $table){
            $table->integer('academicStaffID');
            $table->foreign('academicStaffID')
                ->references('ID')->on('academicStaff');
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->primary(['academicStaffID', 'course_code']);
        });
        Schema::connection('mysql_curriculum')->create('instructor', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');
            $table->integer('academicStaffID');
            $table->foreign('academicStaffID')
                ->references('ID')->on('academicStaff');

            $table->primary(['academicStaffID', 'course_code']);
        });
        Schema::connection('mysql_curriculum')->create('approach', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->tinyInteger('ID');
            $table->string('approach');
            $table->text('description');

            $table->primary(['course_code', 'ID']);
        });
        Schema::connection('mysql_curriculum')->create('reference', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->tinyInteger('ID');
            $table->text('description');

            $table->primary(['course_code', 'ID']);
        });
        Schema::connection('mysql_curriculum')->create('formativeFeedback', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->text('description');

            $table->primary(['course_code']);
        });

        Schema::connection('mysql_curriculum')->create('schedule', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references(['code'])->on('course')
                ->onDelete('cascade');

            $table->tinyInteger('weekID');
            $table->string('topic');
            $table->string('readings');
            $table->string('activities');

            $table->primary(['course_code', 'weekID']);
        });
        Schema::connection('mysql_curriculum')->create('schedule_lo', function (Blueprint $table){
            $table->string('course_code');
            $table->tinyInteger('weekID');
            $table->foreign(['course_code', 'weekID'])
                ->references(['course_code', 'weekID'])->on('schedule')
                ->onDelete('cascade');

            $table->tinyInteger('loID');
            $table->foreign(['course_code', 'loID'])
                ->references(['course_code', 'ID'])->on('learningOutcomes')
                ->onDelete('cascade');

            $table->primary(['course_code', 'weekID', 'loID']);
        });
        Schema::connection('mysql_curriculum')->create('appendix', function (Blueprint $table){
            $table->string('course_code');
            $table->foreign(['course_code'])
                ->references('code')->on('course')
                ->onDelete('cascade');
            $table->string('ID', 3);
            $table->string('header', 255);
            $table->text('description');

            $table->primary(['course_code', 'ID']);
        });
        Schema::connection('mysql_curriculum')->create('criteria', function (Blueprint $table){
            $table->string('course_code');
            $table->string('appendixID', 3);
            $table->foreign(['course_code', 'appendixID'])
                ->references(['course_code', 'ID'])->on('appendix')
                ->onDelete('cascade');

            $table->string('ID', 3);
            $table->text('header');
            $table->text('fail');
            $table->text('pass');
            $table->text('high');

            $table->primary(['course_code', 'appendixID', 'ID']);
        });

        Schema::connection('mysql_curriculum')->create('common', function (Blueprint $table){
            $table->string('title', 100);
            $table->text('description');

            $table->primary(['title']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_curriculum')->dropIfExists('criteria');
        Schema::connection('mysql_curriculum')->dropIfExists('appendix');
        Schema::connection('mysql_curriculum')->dropIfExists('schedule_lo');
        Schema::connection('mysql_curriculum')->dropIfExists('schedule');
        Schema::connection('mysql_curriculum')->dropIfExists('common');
        Schema::connection('mysql_curriculum')->dropIfExists('formativeFeedback');
        Schema::connection('mysql_curriculum')->dropIfExists('reference');
        Schema::connection('mysql_curriculum')->dropIfExists('approach');
        Schema::connection('mysql_curriculum')->dropIfExists('instructor');
        Schema::connection('mysql_curriculum')->dropIfExists('author');
        Schema::connection('mysql_curriculum')->dropIfExists('academicStaff');
        Schema::connection('mysql_curriculum')->dropIfExists('gradAttr_percent');
        Schema::connection('mysql_curriculum')->dropIfExists('lo_gradAttr');
        Schema::connection('mysql_curriculum')->dropIfExists('rubrics');
        Schema::connection('mysql_curriculum')->dropIfExists('assessment_gradAttr');
        Schema::connection('mysql_curriculum')->dropIfExists('assessment_LO');
        Schema::connection('mysql_curriculum')->dropIfExists('assessment_category');
        Schema::connection('mysql_curriculum')->dropIfExists('assessment');
        Schema::connection('mysql_curriculum')->dropIfExists('contentAttDetails');
        Schema::connection('mysql_curriculum')->dropIfExists('contentAtt');
        Schema::connection('mysql_curriculum')->dropIfExists('content');
        Schema::connection('mysql_curriculum')->dropIfExists('graduateAttributes');
        Schema::connection('mysql_curriculum')->dropIfExists('learningOutcomes');
        Schema::connection('mysql_curriculum')->dropIfExists('objectives');
        Schema::connection('mysql_curriculum')->dropIfExists('contactHour');
        Schema::connection('mysql_curriculum')->dropIfExists('prerequisite');
        Schema::connection('mysql_curriculum')->dropIfExists('course');
    }
}
