<?php 
$query_result_students = "SELECT groups.name as group_name, concat_ws('.', concat_ws(' ', students.surname, LEFT(students.name, 1)), 
concat(LEFT(students.patronymic, 1), '.')) AS student, students.number, groups_subjects.exam_test, 
count(academic_performance.mark) AS count_mark, min(academic_performance.mark) AS min_mark, sum(academic_performance.mark) AS sum_mark,
if((groups_subjects.exam_test = 'зачет' AND min(academic_performance.mark) = 1) OR (groups_subjects.exam_test = 'экзамен' AND min(academic_performance.mark > 2)), 'да', 'нет') as passed,
if(groups_subjects.exam_test = 'экзамен' AND min(academic_performance.mark) = 5, 200, if(groups_subjects.exam_test = 'экзамен' AND sum(academic_performance.mark) = count(academic_performance.mark)*5-1, 150, if(groups_subjects.exam_test = 'экзамен' AND min(academic_performance.mark > 3), 100, if(groups_subjects.exam_test = 'зачет' AND min(academic_performance.mark) = 1, 1, 0)))) as grants
FROM groups_subjects INNER JOIN  academic_performance ON groups_subjects.id = academic_performance.group_subject_id
INNER JOIN groups ON groups_subjects.group_id = groups.id
INNER JOIN students ON academic_performance.student_id = students.id
GROUP BY groups_subjects.group_id, academic_performance.student_id, groups_subjects.exam_test";


// TESTER
// $query_stepa = "SELECT if(count(if((groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 4) OR (groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 0), true , NULL)) = 0, 'ok | ', 'not ok | ') as tester 
$query_stepa = "SELECT groups.name as group_name, students.number,
  concat_ws('.', concat_ws(' ', students.surname, LEFT(students.name, 1)), concat(LEFT(students.patronymic, 1), '.')) AS student, 
  if(count(if((groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 0) OR (groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 3), true, NULL)) = 0, 'да', 'нет') as passed,
  CASE 
  WHEN sum(if(groups_subjects.exam_test = 'экзамен', 5, if(groups_subjects.exam_test = 'зачет', 1, 0))) = sum(academic_performance.mark) THEN '200'
  WHEN sum(if(groups_subjects.exam_test = 'экзамен', 5, if(groups_subjects.exam_test = 'зачет', 1, 0))) - 1 = sum(academic_performance.mark) THEN '150'
  WHEN count(if((groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 4) OR (groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 0), true , NULL)) = 0 THEN '100'
  -- GOOD
  -- WHEN count(if(groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 4, true , if(groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 0, true, NULL))) = 0 THEN '100'
  -- WHEN min(academic_performance.mark) > 0 AND count(if(groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 4, true , NULL)) = 0 THEN '100'
  -- WHEN min(academic_performance.mark) > 0 AND count(if(groups_subjects.exam_test = 'экзамен', if(academic_performance.mark < 4, true, NULL), NULL)) = 0 THEN '100'
  -- WHEN count(case when groups_subjects.exam_test = 'экзамен' AND academic_performance.mark < 4 then true when groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 0 then true end) = 0 THEN '100'
  -- WHEN min(academic_performance.mark) > 0 AND count(case when groups_subjects.exam_test = 'экзамен' then case when academic_performance.mark < 4 then 1 end end) = 0 THEN '100'
  -- WHEN min(academic_performance.mark) > 0 AND sum(case when academic_performance.mark IN ('1', '2', '3') AND groups_subjects.exam_test = 'экзамен' then 1 else 0 end) = 0 THEN '100'
  -- GOOD
  ELSE '0' END as grants
  FROM academic_performance INNER JOIN groups_subjects ON academic_performance.group_subject_id = groups_subjects.id INNER JOIN students ON academic_performance.student_id = students.id INNER JOIN groups ON groups_subjects.id = groups.id GROUP BY academic_performance.student_id, groups_subjects.group_id";







// arrayOfZachetov -> searchNezachetPoZachety -> markStudentsByNumber -> result
// arrayOfEkzamenov -> resultArray -> public
// if(sdalzachet()) {$row['$stepa']}

// $stepa = 0;
// if($total == 'да') {
//   if($max_ball_subjects == $student_ball_subjects) {
//     $stepa = 200%
//   } elseif ($max_ball_subjects == $student_ball_subjects - 1 ) { // attention
//     $stepa = 150%
//   } else { $stepa = 100%
//   }
// }


// BAD queries
//      -- if(groups_subjects.exam_test = 'экзамен' AND min(academic_performance.mark) = 5, 200, if(groups_subjects.exam_test = 'экзамен' AND sum(academic_performance.mark) = count(academic_performance.mark)*5-1, 150, if(groups_subjects.exam_test = 'экзамен' AND min(academic_performance.mark > 3), 100, if(groups_subjects.exam_test = 'зачет' AND min(academic_performance.mark) = 1, 1, 0)))) as stepa
//    -- FROM students INNER JOIN (SELECT * FROM academic_performance INNER JOIN groups_subjects ON academic_performance.group_subject_id = groups_subjects.id WHERE (groups_subjects.exam_test = 'экзамен' AND academic_performance.mark > 3) OR (groups_subjects.exam_test = 'зачет' AND academic_performance.mark = 1)) as hren ON students.id = hren.student_id
// BAD queries

// echo ($row_result_students['exam_test'] == 'зачет' && $row_result_students['min_mark'] == 1)  || ($row_result_students['exam_mark'] == 'экзамен' && $row_result_students['min_mark'] > 2) ? 'да' : 'нет'


?>