@push('styles')
<style>
/* Akademik Modern UI Styles */
.card-modern {
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: none;
    margin-bottom: 1.5rem;
    background-color: #fff;
}
.card-modern .card-header {
    background-color: transparent !important;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-modern .card-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 0;
}
.table-modern {
    margin-bottom: 0;
}
.table-modern th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding: 12px 15px;
}
.table-modern td {
    padding: 12px 15px;
    vertical-align: middle;
}
.btn-modern {
    border-radius: 0.5rem;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.2s;
}
.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.form-control-modern {
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
    padding: 0.5rem 0.75rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.form-control-modern:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    border-color: #80bdff;
}
.badge-modern {
    padding: 0.5em 0.75em;
    border-radius: 0.35rem;
    font-weight: 500;
}
</style>
@endpush
