<?php

	/*
	================================================
	== Category Page
	================================================
	*/

	

	ob_start(); // Output Buffering Start

	session_start();
error_reporting(0);
	$pageTitle = 'Categories';

	if (isset($_SESSION['Username'])) {

		include 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		if ($do == 'Manage') {

			$sort = 'asc';

			$sort_array = array('asc', 'desc');

			if (isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)) {

				$sort = $_GET['sort'];

			}

			$stmt2 = $con->prepare("SELECT * FROM categories WHERE parent = 0 ORDER BY Ordering $sort");

			$stmt2->execute();

			$cats = $stmt2->fetchAll(); 

			if (! empty($cats)) {

			?>

			<h1 class="text-center">Manage Categories</h1>
			<div class="container categories">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-edit"></i> Manage Categories
						<div class="option pull-right">
							<i class="fa fa-sort"></i> Ordering: [
							<a class="<?php if ($sort == 'asc') { echo 'active'; } ?>" href="?sort=asc">Asc</a> | 
							<a class="<?php if ($sort == 'desc') { echo 'active'; } ?>" href="?sort=desc">Desc</a> ]
							<i class="fa fa-eye"></i> View: [
							<span class="active" data-view="full">Full</span> |
							<span data-view="classic">Classic</span> ]
						</div>
					</div>
					<div class="panel-body">
						<?php
							foreach($cats as $cat) {
								echo "<div class='cat'>";
									echo "<div class='hidden-buttons'>";
										echo "<a href='categories.php?do=Edit&catid=" . $cat['ID'] . "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i> Edit</a>";
										echo "<a href='categories.php?do=Delete&catid=" . $cat['ID'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-close'></i> Delete</a>";
									echo "</div>";
									echo "<h3>" . $cat['Name'] . '</h3>';
									echo "<div class='full-view'>";
										echo "<p>"; if($cat['Description'] == '') { echo 'This category has no description'; } else { echo $cat['Description']; } echo "</p>";
										if($cat['Visibility'] == 1) { echo '<span class="visibility cat-span"><i class="fa fa-eye"></i> Hidden</span>'; } 
										if($cat['Allow_Comment'] == 1) { echo '<span class="commenting cat-span"><i class="fa fa-close"></i> Comment Disabled</span>'; }
										if($cat['Allow_Ads'] == 1) { echo '<span class="advertises cat-span"><i class="fa fa-close"></i> Ads Disabled</span>'; }  
									echo "</div>";

									// Get Child Categories
							      	$childCats = getAllFrom("*", "categories", "where parent = {$cat['ID']}", "", "ID", "ASC");
							      	if (! empty($childCats)) {
								      	echo "<h4 class='child-head'>Child Categories</h4>";
								      	echo "<ul class='list-unstyled child-cats'>";
										foreach ($childCats as $c) {
											echo "<li class='child-link'>
												<a href='categories.php?do=Edit&catid=" . $c['ID'] . "'>" . $c['Name'] . "</a>
												<a href='categories.php?do=Delete&catid=" . $c['ID'] . "' class='show-delete confirm'> Delete</a>
											</li>";
										}
										echo "</ul>";
									}

								echo "</div>";
								echo "<hr>";
							}
						?>
					</div>
				</div>
				<a class="add-category btn btn-primary" href="categories.php?do=Add"><i class="fa fa-plus"></i> Add New Category</a>
			</div>

			<?php } else {

				echo '<div class="container">';
					echo '<div class="nice-message">There\'s No Categories To Show</div>';
					echo '<a href="categories.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> New Category
						</a>';
				echo '</div>';

			} ?>

			<?php

		} elseif ($do == 'Add') { ?>

			<h1 class="text-center">Add New Category</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
					<!-- Start Name Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="name" class="form-control" autocomplete="off" required="required" placeholder="Name Of The Category" />
						</div>
					</div>
					<!-- End Name Field -->
					<!-- Start Description Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="description" class="form-control" placeholder="Describe The Category" />
						</div>
					</div>
					<!-- End Description Field -->
					<!-- Start Ordering Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Ordering</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" />
						</div>
					</div>
					<!-- End Ordering Field -->
					<!-- Start Category Type -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Parent?</label>
						<div class="col-sm-10 col-md-6">
							<select name="parent">
								<option value="0">None</option>
								<?php 
									$allCats = getAllFrom("*", "categories", "where parent = 0", "", "ID", "ASC");
									foreach($allCats as $cat) {
										echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
									}
								?>
							</select>
						</div>
					</div>
					<!-- End Category Type -->
					<!-- Start Visibility Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Visible</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="vis-yes" type="radio" name="visibility" value="0" checked />
								<label for="vis-yes">Yes</label> 
							</div>
							<div>
								<input id="vis-no" type="radio" name="visibility" value="1" />
								<label for="vis-no">No</label> 
							</div>
						</div>
					</div>
					<!-- End Visibility Field -->
					<!-- Start Commenting Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Allow Commenting</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="com-yes" type="radio" name="commenting" value="0" checked />
								<label for="com-yes">Yes</label> 
							</div>
							<div>
								<input id="com-no" type="radio" name="commenting" value="1" />
								<label for="com-no">No</label> 
							</div>
						</div>
					</div>
					<!-- End Commenting Field -->
					<!-- Start Ads Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Allow Ads</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="ads-yes" type="radio" name="ads" value="0" checked />
								<label for="ads-yes">Yes</label> 
							</div>
							<div>
								<input id="ads-no" type="radio" name="ads" value="1" />
								<label for="ads-no">No</label> 
							</div>
						</div>
					</div>
					<!-- End Ads Field -->

					<!-- Start Photo Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Photo</label>
						<div class="col-sm-10 col-md-6">
							<input 
								type="file" 
								name="photo" 
								class="form-control" 
								required="required" 
								accept="image/*" />
						</div>
					</div>
					<!-- End Photo Field -->

					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="submit" value="Add Category" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
			</div>

			<?php

		} elseif ($do == 'Insert') {

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				echo "<h1 class='text-center'>Insert Category</h1>";
				echo "<div class='container'>";

				// Get Variables From The Form

				$name 		= $_POST['name'];
				$desc 		= $_POST['description'];
				$parent 	= $_POST['parent'];
				$order 		= $_POST['ordering'];
				$visible 	= $_POST['visibility'];
				$comment 	= $_POST['commenting'];
				$ads 		= $_POST['ads'];

				
				$photoName       = $_FILES['photo']['name']; // Name of the uploaded photo
				$photoTmpName    = $_FILES['photo']['tmp_name']; // Temporary location of the uploaded photo
				$photoSize       = $_FILES['photo']['size']; // Size of the uploaded photo

				$file_extension  = strtolower(end(explode('.', $photoName)));

				$stmt = $con->query("SELECT MAX(id) AS last_file_id FROM files");
				$last_id = $stmt->fetch(PDO::FETCH_ASSOC);
				$file_id = $last_id['last_file_id'] + 1;


				

				// Check If Category Exist in Database
				$check = checkItem("Name", "categories", $name);

				if ($check == 1) {

					$theMsg = '<div class="alert alert-danger">Sorry This Category Is Exist</div>';

					redirectHome($theMsg, 'back');

				} else {

					if($photoName == ""){
						$theMsg = '<div class="alert alert-danger">Sorry There is no photo</div>';

						redirectHome($theMsg, 'back');
					}else{

						//insert file in all_files table to get new id for file
						$stmt = $con->prepare("INSERT INTO `files` (id, extention) VALUES (:file_id, :file_extension)");
						$stmt->bindParam(':file_id', $file_id);
						$stmt->bindParam(':file_extension', $file_extension);
						$stmt->execute();

						$targetDirectory = 'uploads/cat_photo';
						$image = $file_id . '.' . $file_extension;
						$targetFileName  = $targetDirectory . $image;
						move_uploaded_file($photoTmpName, $_SERVER['DOCUMENT_ROOT'] . '/ammar/admin/uploads/cat_photo/' . $file_id . '.' . $file_extension);


						// Insert Category Info In Database
						$stmt = $con->prepare("INSERT INTO 
						categories(Name, Description, parent, Ordering, Visibility, Allow_Comment, Allow_Ads,img)
						VALUES(:zname, :zdesc, :zparent, :zorder, :zvisible, :zcomment, :zads, :zimg)");
						$stmt->execute(array(
						'zname' 	=> $name,
						'zdesc' 	=> $desc,
						'zparent' 	=> $parent,
						'zorder' 	=> $order,
						'zvisible' 	=> $visible,
						'zcomment' 	=> $comment,
						'zads'		=> $ads,
						'zimg'		=> $image
						));
						// Echo Success Message
						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Inserted</div>';
						redirectHome($theMsg, 'back');

					}

				}

			} else {

				echo "<div class='container'>";

				$theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';

				redirectHome($theMsg, 'back');

				echo "</div>";

			}

			echo "</div>";

		} elseif ($do == 'Edit') {

			// Check If Get Request catid Is Numeric & Get Its Integer Value
			$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
			// Select All Data Depend On This ID
			$stmt = $con->prepare("SELECT * FROM categories WHERE ID = ?");
			// Execute Query
			$stmt->execute(array($catid));
			// Fetch The Data
			$cat = $stmt->fetch();
			// The Row Count
			$count = $stmt->rowCount();

			// If There's Such ID Show The Form
			if ($count > 0) { ?>

				<h1 class="text-center">Edit Category</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="catid" value="<?php echo $catid ?>" />
						<!-- Start Name Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Name</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="name" class="form-control" required="required" placeholder="Name Of The Category" value="<?php echo $cat['Name'] ?>" />
							</div>
						</div>
						<!-- End Name Field -->
						<!-- Start Description Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="description" class="form-control" placeholder="Describe The Category" value="<?php echo $cat['Description'] ?>" />
							</div>
						</div>
						<!-- End Description Field -->
						<!-- Start Ordering Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Ordering</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" value="<?php echo $cat['Ordering'] ?>" />
							</div>
						</div>
						<!-- End Ordering Field -->
						<!-- Start Category Type -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Parent?</label>
							<div class="col-sm-10 col-md-6">
								<select name="parent">
									<option value="0">None</option>
									<?php 
										$allCats = getAllFrom("*", "categories", "where parent = 0", "", "ID", "ASC");
										foreach($allCats as $c) {
											echo "<option value='" . $c['ID'] . "'";
											if ($cat['parent'] == $c['ID']) { echo ' selected'; }
											echo ">" . $c['Name'] . "</option>";
										}
									?>
								</select>
							</div>
						</div>
						<!-- End Category Type -->
						<!-- Start Visibility Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Visible</label>
							<div class="col-sm-10 col-md-6">
								<div>
									<input id="vis-yes" type="radio" name="visibility" value="0" <?php if ($cat['Visibility'] == 0) { echo 'checked'; } ?> />
									<label for="vis-yes">Yes</label> 
								</div>
								<div>
									<input id="vis-no" type="radio" name="visibility" value="1" <?php if ($cat['Visibility'] == 1) { echo 'checked'; } ?> />
									<label for="vis-no">No</label> 
								</div>
							</div>
						</div>
						<!-- End Visibility Field -->
						<!-- Start Commenting Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Allow Commenting</label>
							<div class="col-sm-10 col-md-6">
								<div>
									<input id="com-yes" type="radio" name="commenting" value="0" <?php if ($cat['Allow_Comment'] == 0) { echo 'checked'; } ?> />
									<label for="com-yes">Yes</label> 
								</div>
								<div>
									<input id="com-no" type="radio" name="commenting" value="1" <?php if ($cat['Allow_Comment'] == 1) { echo 'checked'; } ?> />
									<label for="com-no">No</label> 
								</div>
							</div>
						</div>
						<!-- End Commenting Field -->
						<!-- Start Ads Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Allow Ads</label>
							<div class="col-sm-10 col-md-6">
								<div>
									<input id="ads-yes" type="radio" name="ads" value="0" <?php if ($cat['Allow_Ads'] == 0) { echo 'checked'; } ?>/>
									<label for="ads-yes">Yes</label> 
								</div>
								<div>
									<input id="ads-no" type="radio" name="ads" value="1" <?php if ($cat['Allow_Ads'] == 1) { echo 'checked'; } ?>/>
									<label for="ads-no">No</label> 
								</div>
							</div>
						</div>
						<!-- End Ads Field -->
						<!-- Start Photo Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Photo</label>
						<div class="col-sm-10 col-md-6">
							<input 
								type="file" 
								name="editphoto" 
								class="form-control" 
								required="required" 
								accept="image/*" />
						</div>
						<?php 
						echo '<img src="../admin/uploads/cat_photo/' . $cat['img'] . '" alt=""  style="width: 52px;height: 50px;border-radius: 5px;">';
						?>
					</div>
				
					<!-- End Photo Field -->
						<!-- Start Submit Field -->
						<div class="form-group form-group-lg">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Save" class="btn btn-primary btn-lg" />
							</div>
						</div>
						<!-- End Submit Field -->
					</form>
				</div>

			<?php

			// If There's No Such ID Show Error Message

			} else {

				echo "<div class='container'>";

				$theMsg = '<div class="alert alert-danger">Theres No Such ID</div>';

				redirectHome($theMsg);

				echo "</div>";

			}

		} elseif ($do == 'Update') {

			echo "<h1 class='text-center'>Update Category</h1>";
			echo "<div class='container'>";

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				// Get Variables From The Form

				$id 		= $_POST['catid'];
				$name 		= $_POST['name'];
				$desc 		= $_POST['description'];
				$order 		= $_POST['ordering'];
				$parent 	= $_POST['parent'];

				$visible 	= $_POST['visibility'];
				$comment 	= $_POST['commenting'];
				$ads 		= $_POST['ads'];

				$photo_Name       = $_FILES['editphoto']['name']; // Name of the uploaded photo
				$photo_TmpName    = $_FILES['editphoto']['tmp_name']; // Temporary location of the uploaded photo
				$photo_Size       = $_FILES['editphoto']['size']; // Size of the uploaded photo

				$fileExtension  = strtolower(end(explode('.', $photo_Name)));
				$stmt2 = $con->query("SELECT MAX(id) AS last_file_id FROM files");
				$last_id = $stmt2->fetch(PDO::FETCH_ASSOC);
				$file_id = $last_id['last_file_id'] + 1;

				if($photo_Name == ""){
					// Update The Database With This Info
					$stmt = $con->prepare("UPDATE categories SET
					Name = ?, 
					Description = ?, 
					Ordering = ?, 
					parent = ?,
					Visibility = ?,
					Allow_Comment = ?,
					Allow_Ads = ? 
					WHERE
					ID = ?");
					$stmt->execute(array($name, $desc, $order, $parent, $visible, $comment, $ads, $id));

					// Echo Success Message
					$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
					redirectHome($theMsg, 'back');
				}else{
					$stmt3 = $con->prepare("SELECT img FROM categories WHERE ID = :catid");
					$stmt3->bindParam(':catid', $id);
					$stmt3->execute();
					$row = $stmt3->fetch(PDO::FETCH_ASSOC);
					$img = $row['img'];

					$file = $_SERVER['DOCUMENT_ROOT'] . '/ammar/admin/uploads/cat_photo/'.$img;
					if (file_exists($file)) {
						// Deleting the file
						unlink($file);
					}
					//insert file in all_files table to get new id for file
					$stmt = $con->prepare("INSERT INTO `files` (id, extention) VALUES (:file_id, :file_extension)");
					$stmt->bindParam(':file_id', $file_id);
					$stmt->bindParam(':file_extension', $fileExtension);
					$stmt->execute();

					$targetDirectory = 'uploads/cat_photo';
					$image = $file_id . '.' . $fileExtension;
					move_uploaded_file($photo_TmpName, $_SERVER['DOCUMENT_ROOT'] . '/ammar/admin/uploads/cat_photo/' . $file_id . '.' . $fileExtension);


					// Update The Database With This Info
					$stmt = $con->prepare("UPDATE categories SET
					Name = ?, 
					Description = ?, 
					Ordering = ?, 
					parent = ?,
					Visibility = ?,
					Allow_Comment = ?,
					Allow_Ads = ?,
					img=?
					WHERE
					ID = ?");
					$stmt->execute(array($name, $desc, $order, $parent, $visible, $comment, $ads,$image, $id));

					// Echo Success Message
					$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
					redirectHome($theMsg, 'back');

				}

			} else {

				$theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';

				redirectHome($theMsg);

			}

			echo "</div>";

		} elseif ($do == 'Delete') {

			echo "<h1 class='text-center'>Delete Category</h1>";
			echo "<div class='container'>";

				// Check If Get Request Catid Is Numeric & Get The Integer Value Of It

				$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

				// Select All Data Depend On This ID

				$check = checkItem('ID', 'categories', $catid);

				// If There's Such ID Show The Form

				if ($check > 0) {

					$stmt = $con->prepare("SELECT img FROM categories WHERE ID = :catid");
					$stmt->bindParam(':catid', $catid);
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$img = $row['img'];

					$file = $_SERVER['DOCUMENT_ROOT'] . '/ammar/admin/uploads/cat_photo/'.$img;
					
					if (file_exists($file)) {
						// Deleting the file
						if (unlink($file)) {
							$stmt = $con->prepare("DELETE FROM categories WHERE ID = :zid");
							$stmt->bindParam(":zid", $catid);
							$stmt->execute();
							$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Deleted</div>';
							redirectHome($theMsg, 'back');
						} else {
							$stmt = $con->prepare("DELETE FROM categories WHERE ID = :zid");
							$stmt->bindParam(":zid", $catid);
							$stmt->execute();
							$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Deleted</div>';
							redirectHome($theMsg, 'back');
						}
					} else {
						$stmt = $con->prepare("DELETE FROM categories WHERE ID = :zid");
						$stmt->bindParam(":zid", $catid);
						$stmt->execute();
						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Deleted</div>';
						redirectHome($theMsg, 'back');
					}

				} else {

					$theMsg = '<div class="alert alert-danger">This ID is Not Exist</div>';

					redirectHome($theMsg);

				}

			echo '</div>';

		}

		include $tpl . 'footer.php';

	} else {

		header('Location: index.php');

		exit();
	}

	ob_end_flush(); // Release The Output

?>