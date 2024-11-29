
<script>
    $(document).on('click','.edit',function(e){
        e.preventDefault();
        var id  = $(this).parent().siblings()[0].value;
        $.ajax({
            url:"<?php echo base_url();?>"+"/getSingleUser/"+id,
            method:"GET",
            success: function(result){
                

                var data= JSON.parse(result)
                $(".updateId").val(data.id);
                $(".updateUsername").val(data.name);
                $(".updateEmail").val(data.email);
            }
        })
    })

    $(document).on('click','.delete',function(e){
        e.preventDefault();
        var id  = $(this).parent().siblings()[0].value; 
        var a = confirm("Are you sure want to delete");
        if(a){
            $.ajax({
            url:"<?php echo base_url();?>"+"/deleteUser",
            method:"POST",
            data:{id:id},
            success: function(res){
                   if(res.includes("1")){
                      return window.location.href = window.location.href;
                }
				
              
                
            }
        })
        }
        
    })
   
    $(document).on('click','.delete_all_data', function() {
		var confirmation = confirm("Are you sure you want to delete?");
		if(confirmation) {
			var checkboxes = $(".data_checkbox:checked");
			console.log(checkboxes);

			if(checkboxes.length > 0) {
				var ids = [];
				checkboxes.each(function() {
					ids.push($(this).val());
				})
				console.log(ids);

				$.ajax({
					url: "<?php echo base_url(); ?>"+"/deleteAllUser",
					method: "POST",
					data : {ids : ids},
					success: function(res) {
						
						if(res.includes("1")){
                            return window.location.href = window.location.href;
                        }
						checkboxes.each(function() {
							$(this).parent().parent().parent().hide(100);
						})
						
					}
				})
			}
		}
	})

	$(document).ready(function(){	
    $('.js-example-basic-single').select2();
	})


</script>


<div class="container-xxl">
	<div class="table-responsive d-flex flex-column">
        <?php
           if(session()->getFlashData("sucess")){
        ?>
		<div class="alert w-50 align-self-center alert-success alert-dismissible fade show" role="alert">
			<?php echo session()->getFlashData("sucess");?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		</div>
        <?php 
        }
        ?>
		<div class="table-wrapper border">
			<div class="table-title bg-info-subtle	">
				<div class="row">
					<div class="col-sm-6">
						<h2><a href="/home" style="text-decoration:none;color:sky-blue;" ><b>CRUD</b></a></h2>
					</div>
					<div class="col-sm-6">
						
						<form class="form-inline">
                         <input class="form-control" style="height:30px" name="search" type="search" placeholder="Search" aria-label="Search">
                         <button class="btn btn-outline-success btn-primary  my-sm-0" type="submit">Search</button>
						 
						  <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal" style="margin-left:50px"><i class="material-icons">&#xE147;</i></a>
						  <a href="#deleteEmployeeModal" class="delete_all_data btn btn-danger" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>						
                          <a style="text-decoration:none;color:white;text-algin:center;" href="#filterModal" data-toggle="modal"> 
						  <button style="height: 35px;margin-left:15px;background-color:sky-blue;">
							Filter
						  </button>
						  </a>
						<h5 style="margin-top:10px;margin-left:50px;text-decoration:none;color:white"><a href="/logout">Logout</a></h5>
						    
					</form>
					</div>
				</div>
			</div>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>
							<span class="custom-checkbox">
								<input type="checkbox" id="selectAll">
								<label for="selectAll"></label>
							</span>
						</th>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
                    <?php 
                       if($users){
                        foreach($users as $user){
                   ?>
					<tr>
                        <input type="hidden" id="userId" name="id" value = "<?php echo $user['id'];?>" >
						<td>
							<span class="custom-checkbox">
								<input type="checkbox" id="data_checkbox" class="data_checkbox" name="data_checkbox" value="<?php echo $user['id']?>">
								<label for="data_checkbox"></label>
							</span>
						</td>
						<td><?php echo $user['id'];?></td>
						<td><?php echo $user['name'];?></td>
						<td><?php echo $user['email'];?></td>
						<td>
							<a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
							<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
						</td>
					</tr>
				    <?php 
                      }
                    }
                    ?>
				</tbody>
			</table>
			<?php 
			 if(count($users) ==0){
				echo "<h3 style='color: blue;text-align:center'>No users found</h3>";
			 } 
			?>
			<div class="d-flex justify-content-center align-items-center">
				<ul class="pagination">
					<?= $pager->links('group1', 'bs_pagination') ?>
				</ul>
				

			</div>
			<form class="download"> 
	               <button type="Submit" ><a href="/download" style="text-decoration:none;color:white;">Download</a></button>
				   <button type=""><a href="#uploadModal" data-toggle="modal" style="text-decoration:none;color:white;">Upload</a></button>
            </form>
		</div>
	</div>        
</div>

<!-- Add Modal HTML -->
<div id="addEmployeeModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action = "<?php echo base_url().'/saveUser';?>" method = "POST" >
				<div class="modal-header">						
					<h4 class="modal-title">Add Employee</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">					
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" name="name" required>
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="email" class="form-control" name="email" required>
					</div>				
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" name="submit" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-success" value="Add">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Edit Modal HTML -->
<div id="editEmployeeModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action = <?php echo base_url().'/updateUser'?> method = "POST">
				<div class="modal-header">						
					<h4 class="modal-title">Edit Employee</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
                    <input type="hidden" name="updateId" class = "updateId" >					
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control updateUsername" name = "name" required>
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="text" class="form-control updateEmail" name = "email"  required>
                    </div>			
				</div>
				<div class="modal-footer">
					<input type="button" name = "submit" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-info" value="Save">
				</div>
			</form>
		</div>
	</div>
</div>
<!--modal-->
<div id="filterModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form >
				<div class="modal-header">						
					<h4 class="modal-title">Filter Employee</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
				<select class="js-example-basic-single" name="name">
				
                   <option value="" class="form-control" >Username</option>
                   <?php foreach($all_users as $user){ ?>
					<option value="<?php echo $user['name']; ?>"><?php echo $user['name'];?>
					</option>
					<?php }?>
                </select>	
				</div>
				<div class="modal-body">
				<select class="js-example-basic-single" name="id">
				
                   <option value="" class="form-control" >ID</option>
                   <?php foreach($all_users as $user){ ?>
					<option value="<?php echo $user['id']; ?>"><?php echo $user['id'];?>
					</option>
					<?php }  ?>
                </select>	
				</div>
				<div class="modal-body">
				<select class="js-example-basic-single" name="email">
				
                   <option value="" class="form-control" >Email</option>
                   <?php foreach($all_users as $user){ ?>
					<option value="<?php echo $user['email']; ?>"><?php echo $user['email'];?>
					</option>
					<?php }  ?>
                </select>	
				</div>
				<div class="modal-footer">
					<input type="button" name ="submit" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-info" value="Search">
				</div>
			</form>
		</div>
	</div>
</div>
<!--uploadmodal--->
<div id="uploadModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action = "<?php echo base_url().'/uploadFile';?>" method = "POST" enctype="multipart/form-data">
				<div class="modal-header">						
					<h4 class="modal-title">Upload Csv File</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
				   <div class="form-group">
				   <input type="file" name="uploadFile">
				   </div>
				   <div >
					<input type="submit"  value="Upload">
				</div>
				</div>
			</form>
		</div>
	</div>
</div>

