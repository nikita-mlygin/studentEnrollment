delimiter //

drop procedure if exists `_tmp_procedure`;

create procedure _tmp_procedure()
begin
	declare speciality_id int;
    declare speciality_student_count int;
    declare student_speciality int;
    declare student_priority int;

	declare student_id int;
    declare curs1_end boolean default false;
	declare curs1 cursor for
		select group_id, sum(group_student_count) from sceciality_group group by ref_group_cpecialisty_id;
	
end//

call _tmp_procedure();