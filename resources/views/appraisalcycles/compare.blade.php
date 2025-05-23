@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Compare Employees</h4>
                    </div>
                </div>
            </div>



            <div class="col-lg-12 my-2 ">


                <div class="d-flex justify-content-between mb-3">
                    <input type="text" id="searchBox" class="form-control search-input" placeholder="Search employee...">
                    <button class="btn btn-outline-secondary ms-2" id="filterSelected">View Selected Only</button>
                </div>

                <div class="row g-4" id="employeeCards">

                </div>
                <div class="pagination-container text-center">
                    <button class="btn btn-primary me-2" id="prevPage">Previous</button>
                    <span id="pageInfo" class="fw-bold"></span>
                    <button class="btn btn-primary ms-2" id="nextPage">Next</button>
                </div>
            </div>

        </div>
    </div>

</div>

</div>


@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style type="text/css">
     .employee-card {
      border-left: 5px solid #0d6efd;
      transition: transform 0.2s;
    }
    .employee-card:hover {
      transform: scale(1.02);
    }
    .employee-header {
      background-color: #e9f2ff;
      position: sticky;
      top: 0;
      z-index: 2;
      padding: 1rem;
      border-radius: .5rem .5rem 0 0;
    }
    .role-item {
      display: flex;
      align-items: center;
    }
    .role-item i {
      color: #6c757d;
      margin-right: .5rem;
    }
    .search-input {
      max-width: 400px;
    }
    .employee-entry {
      display: none;
    }
    .pagination-container {
      margin-top: 2rem;
    }
</style>
@endsection

@section('js')


<script>

    // Safely pass the PHP data to JavaScript
    const allEmployees = @json($users);
    const perPage = 3;
    let currentPage = 1;
    let showSelectedOnly = false;
    const selectedEmployees = new Set();



    function renderEmployees() {
        const start = (currentPage - 1) * perPage;
        const filtered = allEmployees.filter(empuser =>
        empuser.employee.employee_name.toLowerCase().includes($('#searchBox').val().toLowerCase()) &&
        (!showSelectedOnly || selectedEmployees.has(empuser.id))
        );
        {{-- console.log(filtered); --}}
        const employeesToShow = filtered.slice(start, start + perPage);

        $('#employeeCards').empty();
        employeesToShow.forEach(empuser => {

        const assessees = empuser.assessees.map(a => `<li class="list-group-item role-item"><i class="bi bi-person-circle"></i> ${a.assesseeuser.employee.employee_name}</li>`).join('');
        const assessors = empuser.assessors.map(a => `<li class="list-group-item role-item"><i class="bi bi-person-circle"></i> ${a.assessoruser.employee.employee_name}</li>`).join('');
        const card = `
            <div class="col-lg-4 employee-entry" data-name="${empuser.name.toLowerCase()}">
                <div class="card shadow-sm employee-card">
                    <div class="employee-header">
                        <div class="form-check float-right">
                            <input class="form-check-input selectCheckbox" type="checkbox" value="${empuser.id}" ${selectedEmployees.has(empuser.id) ? 'checked' : ''}>
                        </div>
                        <h5 class="mb-1"><i class="bi bi-person-badge-fill mr-2"></i>${empuser.employee.employee_name}</h5>
                        <small>${empuser.employee.position.name} - ${empuser.employee.department.name} (${empuser.employee.employee_code})</small>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-success w-100 mb-2" data-toggle="collapse" data-target="#assessees${empuser.id}">
                            <i class="bi bi-people-fill me-1"></i> Assessees <span class="badge bg-success">${empuser.assessees.length}</span>
                        </button>
                        <div id="assessees${empuser.id}" class="collapse">
                            <ul class="list-group list-group-flush">${assessees}</ul>
                        </div>

                        <button class="btn btn-outline-primary w-100 mb-2" data-toggle="collapse" data-target="#assessors${empuser.id}">
                            <i class="bi bi-person-check-fill me-1"></i> Assessors <span class="badge bg-primary">${empuser.assessors.length}</span>
                        </button>
                        <div id="assessors${empuser.id}" class="collapse">
                            <ul class="list-group list-group-flush mb-3">${assessors}</ul>
                        </div>

                    </div>
                </div>
            </div>
        `;
        $('#employeeCards').append(card);
        });

        $('#employeeCards .employee-entry').show(); // 🔥 Make sure they show up
        $('#pageInfo').text(`Page ${currentPage} of ${Math.ceil(filtered.length / perPage)}`);
    }
    $(document).ready(function() {
        console.log(allEmployees);

        renderEmployees();

        $('#searchBox').on('input', function() {
            currentPage = 1;
            renderEmployees();
        });

          $('#prevPage').on('click', function() {
            if (currentPage > 1) {
              currentPage--;
              renderEmployees();
            }
        });

        $('#nextPage').on('click', function() {
            const filteredCount = allEmployees.filter(emp =>
                emp.name.toLowerCase().includes($('#searchBox').val().toLowerCase()) &&
                (!showSelectedOnly || selectedEmployees.has(emp.id))
            ).length;
            if (currentPage < Math.ceil(filteredCount / perPage)) {
                currentPage++;
                renderEmployees();
            }
        });


        $('#employeeCards').on('change', '.selectCheckbox', function() {
            const id = parseInt($(this).val());
            if ($(this).is(':checked')) {
              selectedEmployees.add(id);
            } else {
              selectedEmployees.delete(id);
            }
          });

          $('#filterSelected').on('click', function() {
            showSelectedOnly = !showSelectedOnly;
            currentPage = 1;
            $(this).toggleClass('btn-outline-secondary btn-outline-success');
            $(this).text(showSelectedOnly ? 'Show All' : 'View Selected Only');
            renderEmployees();
          });

    });

</script>
@stop
