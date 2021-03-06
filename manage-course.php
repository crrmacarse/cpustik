<?php

	session_start();
	include('library/form/connection.php');
	include('library/functions/functions.php');
	$db = new db();

	include('library/functions/checkSession.php');

	$search = isset($_GET['search']) ? '%'.$_GET['search'].'%' : '%%';
	$trimmedsearch = str_replace('%', '', $search);
	$getcollege = isset($_GET['college']) ? $_GET['college'] : '';	

	$list_getCollege = $db->connection->prepare('SELECT College.CName FROM College WHERE College.idCollege = ?');
	$list_getCollege->bindparam(1, $getcollege);
	$list_getCollege->execute();
	$college = $list_getCollege->fetchColumn();
	
	$getCount = $db->connection->prepare('SELECT COUNT(*) FROM Course WHERE Course.idCourse = ?');
	$getCount->bindparam(1,$getcollege);
	$getCount->execute();
	$total_count = $getCount->fetchColumn(); 
	$total_page = ceil($total_count/10);

	$page = isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $total_page ? $_GET['page'] : '1';
	$offset = $page * 10;
	$limit =  ($page * 10) - 9;


	if($page < 2) {
		$disable_previous = 'disabled text-muted';
		$disable_previous2 = 'pointer-events: none;';
		$bottom_page = 'display:none';
	}
	else {
		$disable_previous = '';
		$disable_previous2 = '';
		$bottom_page = '';
	}
	if($total_page < 2) {
		$top_page = 'display:none';
	}
	else {
		$top_page = '';
	}
	if($total_page == $page || $total_page < 1) {
		$disable_next = 'disabled text-muted';
		$disable_next2 = 'pointer-events: none;';
		$top_page = 'display:none';
	}
	else {
		$disable_next = '';
		$disable_next2 = '';
		$top_page = '';
	}
?>

<!doctype html>
<html lang="en">
	
	<head>
	
	   	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<title>CPU Smart Touch Information Kiosk</title>

		<link href = "library/css/bootstrap.min.css" rel = " stylesheet">
		<link href = "library/css/mystyles.css" rel = "stylesheet">
		
	</head>

<body>

<body>

	<div class="modal fade" id="addCourseForm_MODAL" tabindex="-1" role="dialog" aria-labelledby="Add Course Modal" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="modalTitle">Add Course for <?php echo $college ?></h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	         <form id = "addCourseForm" class="form-horizontal" action="library/form/frmAddCourse.php" method="post" 
	         name="upload_excel" enctype="multipart/form-data">
	         <input id = "addCourseForm_COLLEGE" type = "text" name = "COLLEGE" value = "<?php echo  $getcollege ?>" hidden />
	         <div class="panel panel-default">
							<div class="panel-body">
 								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroup-sizing-sm">Course Code</span>
									  </div>
									  <input id = "addCourseForm_CODE" name = "CODE" type="input" class="form-control" placeholder="Course Code" aria-describedby="sizing-addon2" required>
								</div>
								<br />
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroup-sizing-sm">Course Abbreviation</span>
									  </div>
									  <input id = "addCourseForm_NAMEABBR" name = "NAMEABBR" type="input" class="form-control" placeholder="Course Abbreviation" aria-describedby="sizing-addon2" required>
								</div>
								<br />
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroup-sizing-sm">Course Name</span>
									  </div>
									  <input id = "addCourseForm_NAME" name = "NAME" type="input" class="form-control" placeholder="Course Name" aria-describedby="sizing-addon2" required>
								</div>
								<br />
					</div>
				</div>
		      </div>
		      <div class="modal-footer">
		        <input type="submit" id = "addCourseForm_SUBMIT" class="btn btn-success" name = "ADD" data-loading-text = "Adding.." value = "Add">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		      </div>
	  </form>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="deleteCourseForm_MODAL" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	           <div class="modal-header">
					<h5>Delete Course</h5>
				</div>
	            <form id="deleteCourseForm" method="post" action="library/form/frmDeleteCourse.php">
	                <div class="modal-body">
	                    <div><input type="text" id="deleteCourseForm_ID" name="ID" style="display: none;"></div>
	                    <p>Do you want to delete this record?</p>
	                    <table class="table">
	                        <thead>
	                        <tr>
	                            <td></td>
	                            <td><b>Course Details</b></td>
	                        </tr>
	                        </thead>
	                        <tbody>
	                        <tr>
	                            <td>Course Abbreviation: </td>
	                            <td id="deleteCourseForm_NAMEABBR"></td>
	                        </tr>
	                        <tr>
	                            <td>Course Name: </td>
	                            <td id="deleteCourseForm_NAME"></td>
	                        </tr>
	                        <tr>
	                            <td>College: </td>
	                            <td id="deleteCourseForm_COLLEGE" class = "font-italic"></td>
	                        </tr>
	                       	<tr>
	                            <td>Date Created: </td>
	                            <td id="deleteCourseForm_DATECREATED"></td>
	                        </tr>
	                        <tr>
	                            <td>Status:</td>
	                            <td id="deleteCourseForm_STATUS"></td>
	                        </tr>	
							</tbody>
	                    </table>
	                </div>
	                <div class="modal-footer">
						<button type="submit" id="deleteCourseForm_SUBMIT" class="btn btn-danger" data-loading-text="Deleting..."> Delete</button>
	                    <button type="button" class="btn btn-default" data-dismiss="modal"></span> Close</button>
	                   
	                </div>
	            </form>
	        </div>
	    	</div>
		</div>

	
	<div class="container">
			<div class = "row top-buffer">
				<div class = "col-lg-6">
					<h1 class = "h3">Course Management for <?php echo $college ?></h1>
					<p class = "text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc non mauris vitae dui lacinia cursus eget eu urna.</p>
				</div>
				
				<div class = "col-lg-6">
					<div class = "float-right button-manage-group">
						<a href = "manage-college.php" class="btn btn-info"><i class="fas fa-arrow-left"></i>&nbsp; Return</a>
						<span class="dropdown">
						  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    Actions
						  </button>
						  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						    <a href = "#" data-target = "#addCourseForm_MODAL" class = "dropdown-item"data-toggle = "modal">Add Course</a>
						    <a class="dropdown-item" href="mailto:totopaulmanares@yahoo.com?Subject=CPU%20Touch%20Information%20Kiosk%20Concerns" target="_top">Report</a>
						  </div>
						</span>
					</div>
				</div>
			</div>
		
		<div class = "row">
			<div class = "col-lg-12">
						<hr class="my-4 float-right" width="65%">
			</div>
			<div class = "col-lg-8">
			</div>
			<div class = "col-lg-4">
				<div class = "input-group">
				<input type = "text" id = "txtSearch" class = "form-control" placeholder="Search">
				&nbsp;
				<button id = "btnSearch" class = "btn btn-success">Search</button>
				</div>
			</div>
		</div>

		<div class = "row top-buffer">
			<table class="table table-striped">
			  <thead>
				<tr>
				  <th scope="col">Course Code</th>
				  <th scope="col">Course ABBR</th>
				  <th scope="col">Course Name</th>
				  <th scope="col">Status</th>
				  <th scope="col">Actions</th>
				</tr>
			  </thead>
			  <tbody id = "courseList">
			  </tbody>
			</table>			
		</div>

		<div class = "row top-buffer justify-content-md-center">
		 <div class="col-md-auto top-buffer">
			<nav aria-label="Page navigation example">
				  <ul class="pagination justify-content-center">
				    	<li class="page-item">
						  <a class="page-link <?php echo $disable_previous; ?>" style="<?php echo $disable_previous2; ?>" href="manage-course.php?search=<?php echo $trimmedsearch; ?>&page=<?php echo $page - 1; ?>" tabindex="-1">Previous</a>
						</li>
						<?php   	
						  	for($i = 1; $i <= $total_page; $i++)
							{
								?>
						  	<li id = "paginationActive<?php echo $i; ?>" class="page-item">
								<a class="page-link" href="manage-course.php?search=<?php echo $trimmedsearch; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
						    </li>
						  
						  <?php
							}
						  ?>
						<li class="page-item">
						  <a class = "page-link <?php echo $disable_next; ?>" style="<?php echo $disable_next2; ?>" href="manage-course.php?search=<?php echo $trimmedsearch; ?>&page=<?php echo $page + 1; ?>" >Next</a>
						</li>			  </ul>
				</nav>
			</div>
			</div>

		<?php include('library/html/footer.html'); ?>

	</div>
	
  <script src = "library/js/jquery-3.3.1.min.js"></script>
  <script src="library/js/popper.min.js"></script>
  <script src="library/js/bootstrap.min.js"></script>
  <script src = "library/js/jquery.form.js"></script>
  <script src = "library/js/app.js"></script>	
  <script src="library/js/messagealert.js"></script>
  <script src="library/js/all.js"></script>
</body>
	
	
</html>

<script>

	$(document).ready(function(){
		$("#paginationActive<?php echo $page ?>").addClass("active");
		$("#txtSearch").val("<?php echo $trimmedsearch ?>");
			 $( "#btnSearch" ).click(function() {
			  var searchValue = $("#txtSearch").val().toLowerCase(); 
				window.location.href='manage-course.php?college=<?php echo $getcollege ?>&search='+searchValue;
				});
				$('#txtSearch').keypress(function(e){
				if(e.which == 13){ 
					$('#btnSearch').click();
				}
			});

			$("#txtSearch").click(function(){
				$("#txtSearch").val("");
			})
		
	});

	var PageComponent = {
        courseList: document.getElementById('courseList')
    };


    var ACForm = {
    	form: document.getElementById('addCourseForm'),
    	code: document.getElementById('addCourseForm_CODE'),
    	nameabbr: document.getElementById('addCourseForm_NAMEABBR'),
    	name: document.getElementById('addCourseForm_NAME'),
    	college: document.getElementById('addCourseForm_COLLEGE'),
    	modal: '#addCourseForm_MODAL',
    	submit: '#addCourseForm_SUBMIT'
    }
 
	ACForm.form.onsubmit = function (e)
	{
		e.preventDefault();
		$(this).ajaxSubmit({
			beforeSend:function()
			{
				$(ACForm.submit).button('loading');
			},
			
			uploadProgress:function(event,position,total,percentComplete)
			{
				
			},
			success:function(data)
			{
				$(ACForm.submit).button('reset');
				var server_message = data.trim();	
				if(!isWhitespace(GetSuccessMsg(server_message)))
					{
						$(ACForm.modal).modal('hide');
						alert('Added Succesfully');
						window.location.reload(false); 						
						ACForm.form.reset();
					}
				else if(!isWhitespace(GetWarningMsg(server_message)))
					{
						alert(GetWarningMsg(server_message));
					}
				else if(!isWhitespace(GetErrorMsg(server_message)))
					{
						alert(GetErrorMsg(server_message));
					}
				else if(!isWhitespace(GetServerMsg(server_message)))
					{
						alert(GetServerMsg(server_message));
					}
				else
					{
						alert('Oh Snap! There is a problem with the server or your connection.');
					}
				}
			});
		};


	var DCForm = {
		form: document.getElementById('deleteCourseForm'),
		modal: document.getElementById('deleteCourseForm_MODAL'),
		id: document.getElementById('deleteCourseForm_ID'),
		nameabbr: document.getElementById('deleteCourseForm_NAMEABBR'),
		name: document.getElementById('deleteCourseForm_NAME'),
		college: document.getElementById('deleteCourseForm_COLLEGE'),
		datecreated: document.getElementById('deleteCourseForm_DATECREATED'),
		status: document.getElementById('deleteCourseForm_STATUS'),
		submit: document.getElementById('deleteCourseForm_SUBMIT')
	}
	
	$(DCForm.form).on('submit', function (e) {
        var id = DCForm.id.value;

        e.preventDefault();
        $(this).ajaxSubmit({
            beforeSend:function()
            {
                $(DCForm.submit).button('loading');
            },
            uploadProgress:function(event,position,total,percentCompelete)
            {

            },
            success:function(data)
            {

                $(DCForm.submit).button('reset');
				deleteCourse(id);
				DCForm.form.reset();
				$(DCForm.modal).modal('hide');
				alert('Succesfully Deleted');
            }
        });
    });
	

	function deleteCourse(id){
		$('#course_'+id).remove();
	}

    function openDeleteCourseModal(id) {
        DCForm.id.value = id;
        DCForm.nameabbr.innerHTML = document.getElementById('coures_NAMEABBR_'+id).innerHTML;
        DCForm.name.innerHTML = document.getElementById('coures_NAME_'+id).innerHTML;
        DCForm.college.innerHTML = document.getElementById('course_COLLEGE_'+id).innerHTML;
        DCForm.datecreated.innerHTML = document.getElementById('course_DATECREATED_'+id).innerHTML;
        DCForm.status.innerHTML = document.getElementById('course_STATUS_'+id).innerHTML;
        $(DCForm.modal).modal('show');
    }

    function addCourseList(id, code, college, nameabbr, name, datecreated, status)
    {
    	PageComponent.courseList.innerHTML = PageComponent.courseList.innerHTML +
    		'<thead>'+
			'<tr id = "course_'+ id +'">'+
			'	<td scope = "col" id = "coures_CODE_' + id +'">' + code + '</td>'+
			'	<td scope = "col" id = "coures_NAMEABBR_' + id +'">' + nameabbr + '</td>'+
			'	<td scope = "col" id = "coures_NAME_' + id +'">' + name + '</td>'+
			'	<td scope = "col" id = "course_STATUS_' + id + '">' + status + '</td>'+
			'	<td scope = "col" id = "course_DATECREATED_' + id + '" hidden>' + datecreated + '</td>'+
			'	<td scope = "col" id = "course_COLLEGE_' + id + '" hidden>' + college + '</td>'+
			// ' 	<td><div class = "btn-group" role = "group"><button id="course_BTNUPDATE_' + id + '" value="' + id + '" data-target = "#updateCourseForm_MODAL" data-toggle = "modal" onclick = "updateCourseFill(\'' + id + '\')"class="btn btn-primary"><i class="far fa-edit"></i></button><button id="course_BTNDELETE_' + id + '" value="' + id + '" class="btn btn-warning ml-1" role = "button" onclick="openDeleteCourseModal(' + id + ')"><i class="far fa-trash-alt"></i></button></div></td>'+
			' 	<td><button id="course_BTNDELETE_' + id + '" value="' + id + '" class="btn btn-warning ml-1" role = "button" onclick="openDeleteCourseModal(' + id + ')"><i class="far fa-trash-alt"></i></button></div></td>'+
			'</tr>'+	
			'</thead>';
    }

	<?php 

	$list_sql = '
				WITH OrderedList AS
				(
				SELECT 			 
				
				Course.idCourse,
				Course.CCode,
				College.CName AS COLLEGENAME,
				Course.CNameAbbr,
				Course.CName,
				Course.CDateCreated,
				Course.CStatus,
				ROW_NUMBER() OVER (ORDER BY Course.CName) AS "RowNumber"
								
				From Course
				
				INNER JOIN College
				ON College.idCollege = Course.idCollege

				WHERE(Course.CName LIKE ? OR Course.CNameAbbr LIKE ?) AND Course.idCollege = ?
				)

				SELECT * 
				FROM OrderedList 
				WHERE RowNumber BETWEEN ? AND ?';
	
	$list_getResult = $db->connection->prepare($list_sql);
	$list_getResult->bindparam(1, $search);
	$list_getResult->bindparam(2, $search);
	$list_getResult->bindparam(3, $getcollege);
	$list_getResult->bindparam(4, $limit);
	$list_getResult->bindparam(5, $offset);
	$list_getResult->execute();
	$list_count = $list_getResult->RowCount();

	if($list_count > 0){
		?>

			var content = '<tr>'+
			'<tr>'+
			'<td id = "course_TITLE_">No Courses Found</td>'+
			'<tr>';

			$("#courseList").append(content);

		<?php
	}
	else
	{

		foreach($list_getResult as $list_row)
		{
			$result_ID = htmlspecialchars($list_row['idCourse']);
			$result_CODE = htmlspecialchars($list_row['CCode']);
			$result_COLLEGE = htmlspecialchars($list_row['COLLEGENAME']);
			$result_NAMEABBR = htmlspecialchars($list_row['CNameAbbr']);
			$result_NAME = htmlspecialchars($list_row['CName']);
			$result_DATECREATED = htmlspecialchars($list_row['CDateCreated']);
			$result_STATUS = htmlspecialchars($list_row['CStatus']);
		

		if($result_STATUS == True)
		{
			$result_STATUS = 'Active';
		}
		else
		{
			$result_STATUS = 'Inactive';
		}
	?>

	addCourseList("<?php echo $result_ID ?>","<?php echo $result_CODE ?>","<?php echo $result_COLLEGE ?>","<?php echo $result_NAMEABBR ?>","<?php echo $result_NAME ?>","<?php echo $result_DATECREATED ?>","<?php echo $result_STATUS ?>");
		<?php 
		}			
	}
	?>


</script>	