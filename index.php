<?php

    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'namedb');

    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if($connection === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    // Add
    $name = $details = $error = "";

    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $name = trim($_POST["name"]);
        $details = trim($_POST["details"]);

        if($name == "" | $details == "") {
            $error = "Enter data please.";
        }

        $sql = "INSERT INTO name_list (name, details) VALUES (?, ?)";

        // Check input errors before inserting in database
        if(empty($error)){
            // Prepare an insert statement
            $sql = "INSERT INTO name_list (name, details) VALUES (?, ?)";
            $stmt = mysqli_prepare($connection, $sql);

            if ( !$stmt ) {
              die('mysqli error: '. mysqli_error($connection));
            }

            $param_name = $name;
            $param_address = $details;

            mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_address);

            if ( !mysqli_execute($stmt) ) {
              die( 'stmt error: '.mysqli_stmt_error($stmt) );
            }

            if(mysqli_stmt_execute($stmt)){
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
            mysqli_close($connection);
        }

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Dashboard</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="main.min.css">

    <script src="https://code.jquery.com/jquery-3.3.1.js" charset="utf-8"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" charset="utf-8"></script>

</head>
<body>
    <div class="wrapper">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h6>Add name</h6>
                    </div>

                    <?php if($error !== "") { echo "<p class='text-danger'>". $error ."</p>"; } ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" placeholder="name" class="form-control" value="<?php echo $name; ?>">
                        </div>
                        <div class="form-group">
                            <label>Details</label>
                            <textarea name="details" class="form-control" placeholder="Details"><?php echo $details; ?></textarea>
                        </div>
                        <input type="submit" class="btn btn-light" value="Submit">
                    </form>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h6>Name list</h6>
                    </div>
                    <?php
                    $sql = "SELECT * FROM name_list";
                    if($result = mysqli_query($connection, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table id='name_list' class='table table-light'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Name</th>";
                                        echo "<th>Details</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td>" . $row['details'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
                    }

                    // Close connection
                    mysqli_close($connection);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
	        'use strict';

	        if ($('#name_list').length) {
		        setDataTable();
	        }

	        function setDataTable() {
		        $('#name_list').DataTable({
			        searching: true,
			        bLengthChange: false,
			        destroy: true,
			        info: true,
			        responsive: true,
			        "pagingType": "simple_numbers",
			        dom: '<"row table-filter"<"col-sm-12"<"float-left"l><"float-sm-right"f><"clearfix">>>t<"row table-pagination"<"col-sm-12"<"text-center"ip>>>',
			        language: {
				        paginate: {
					        previous: "<i class='icon-arrow-left'></i>",
					        next: "<i class='icon-arrow-right'></i>"
				        },
				        searchPlaceholder: 'Search...',
				        sSearch: '',
				        lengthMenu: '_MENU_ items/page'
			        },
			        columns: [
				        {
					        className: "first-column"
				        },
				        null,
				        null
			        ],
			        drawCallback: function() {
				        $($(".dataTables_wrapper .pagination li:first-of-type"))
					        .find("a")
					        .addClass("prev");
				        $($(".dataTables_wrapper .pagination li:last-of-type"))
					        .find("a")
					        .addClass("next");

				        $(".dataTables_wrapper .pagination").addClass("pagination-sm");
			        }
		        });

                $('.dataTables_filter input').addClass("form-control filter-table");
	        }
        });
    </script>
</body>
</html>
