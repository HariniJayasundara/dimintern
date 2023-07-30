<!DOCTYPE html>
<html>
<head>
  <title>Manage Assignments</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="../styles.css">
    <script src="../sidebar.js"></script>


  <style type="text/css">

    .filter-container {
      margin-bottom: 10px;

    }

    .filter-input {
      padding: 5px;
      margin-top: 20px;
      padding: 10px;
      margin-left: 20px;
    }

    .filter-btn {
      padding: 5px 10px;
      background-color: #03a68d;
      color: #fff;
      border: none;
      cursor: pointer;
      margin-top: 20px;
      padding: 10px;
      margin-right: 10px;
      margin-left: 10px;
    }


      .main {
  position: absolute;
  top: 60px;
  width: calc(100% - 260px);
  min-height: calc(100vh - 60px);
  left: 260px;
  background: #343a40;

}

    /* Table Styles */
table {
  width: 85%;
  border-collapse: collapse;
  background-color: #fff;
  padding: 20px;
  margin: 20px;
}

th,
td {
  padding: 8px;
  border: 1px solid #ddd;
  text-align: left;
}

th {
  background-color: #f2f2f2;
}

tr:hover {
  background-color: #f9f9f9;
}


  </style>
</head>
<body>
  <div class="container">
            <div class="topbar">
            <div class="logo">
                <h2>DIM Admin</h2>
            </div>
        </div>

        <div class="col-md-2 sidebar">
            <ul>
                <li>
                    <a href="../index.php">
                        <i class="fas fa-th-large"></i>
                        <div>Dashboard</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Students/manage_students.html">
                        <i class="fas fa-user-graduate"></i>
                        <div>Students</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Companies/manage_companies.php">
                        <i class="fas fa-user-tie" ></i>
                        <div>Companies</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Admin/admin.php">
                        <i class="fas fa-users"></i>
                        <div>DIM Staff</div>
                    </a>
                </li>
                <li>
                    <a href="../Manage Preferences/map_preferences.html">
                        <i class="fas fa-hand-sparkles"></i>
                        <div>Preferences</div>
                    </a>
                </li>
                <li>
                    <a href="admin_manageAssignments.php">
                        <i class="fas fa-clipboard-check"></i>
                        <div>Allocations</div>
                    </a>
                </li>
                <li>
                    <a>
                        <i class="fas fa-file-signature"></i>
                        <div>Documentation</div>
                    </a>
                    <ul>
                    <li>
                        <a href="../Manage CVs/admin_view_cv">
                            <i class="far fa-file-alt"></i>
                            <div>CV</div>
                        </a>
                    </li>
                    <li>
                        <a href="../Manage Reports/admin_internReports.php">
                            <i class="far fa-file-alt"></i>
                            <div>User Reports</div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="far fa-file-alt"></i>
                            <div>Generate Report</div>
                    </a>
                    </li>
                    </ul>
                    </li>
            </ul>
            <div class="logout-option">
                <a href="../../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <div>Logout</div>
            </div> </a>
        </div>
  </div>
 <!-- End sidebar        -->

<!-- Main -->
<div class="col-md-10 main">
  <div class="filter-container">
    <input type="text" class="filter-input" id="studentFilter" placeholder="Filter by Student Number">
    <input type="text" class="filter-input" id="preferenceFilter" placeholder="Filter by Preference">
    <input type="text" class="filter-input" id="companyFilter" placeholder="Filter by Company">
    <input type="text" class="filter-input" id="statusFilter" placeholder="Filter by Status">
    <button class="filter-btn" onclick="applyFilters()">Apply Filters</button>
    <button class="filter-btn" onclick="resetFilters()">Reset Filters</button>
  </div>

  <div class="container">
    <div class="card bg-white mt-4">
      <div class="card-body">
        <table class="table table-bordered table-striped" id="assignments-table">
          <thead class="thead-dark">
            <tr>
              <th>Student Number</th>
              <th>Preference</th>
              <th>Company</th>
              <th>Current Status</th>
            </tr>
          </thead>
          <tbody id="assignments-body">
            <!-- Table body content will be populated dynamically -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


</div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
    // The initial data fetched from the server
    var originalAssignments = [];

    $(document).ready(function() {
      // Make an AJAX request to fetch student assignments
      $.ajax({
        url: 'admin_viewAssignments.php',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
          originalAssignments = data.assignments;
          updateTable(originalAssignments);
        },
        error: function() {
          alert('Error: Failed to fetch student assignments.');
        }
      });
    });

    function applyFilters() {
      var studentFilter = $('#studentFilter').val().trim().toLowerCase();
      var preferenceFilter = $('#preferenceFilter').val().trim().toLowerCase();
      var companyFilter = $('#companyFilter').val().trim().toLowerCase();
      var statusFilter = $('#statusFilter').val().trim().toLowerCase();

      var filteredAssignments = originalAssignments.filter(function(assignment) {
        return (
          (assignment.student_number.toLowerCase().indexOf(studentFilter) !== -1) &&
          (assignment.preference_name.toLowerCase().indexOf(preferenceFilter) !== -1) &&
          (assignment.company_name.toLowerCase().indexOf(companyFilter) !== -1) &&
          (assignment.current_status.toLowerCase().indexOf(statusFilter) !== -1)
        );
      });

      updateTable(filteredAssignments);
    }

    function resetFilters() {
      $('#studentFilter').val('');
      $('#preferenceFilter').val('');
      $('#companyFilter').val('');
      $('#statusFilter').val('');

      updateTable(originalAssignments);
    }

    function updateTable(assignments) {
      var assignmentsList = '';
      for (var i = 0; i < assignments.length; i++) {
        var studentNumber = assignments[i].student_number;
        var preference = assignments[i].preference_name;
        var companyName = assignments[i].company_name;
        var currentStatus = assignments[i].current_status;

        assignmentsList += '<tr>';
        assignmentsList += '<td>' + studentNumber + '</td>';
        assignmentsList += '<td>' + preference + '</td>';
        assignmentsList += '<td>' + companyName + '</td>';
        assignmentsList += '<td>' + currentStatus + '</td>';
        assignmentsList += '</tr>';
      }

      $('#assignments-body').html(assignmentsList);
    }
  </script>
</body>
</html>