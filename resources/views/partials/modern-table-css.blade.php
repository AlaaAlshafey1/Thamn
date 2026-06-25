<style>
    .modern-table {
        border-collapse: separate;
        border-spacing: 0 10px;
        background: transparent !important;
    }
    .modern-table tbody tr {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .modern-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        background-color: #fff !important;
    }
    .modern-table td {
        border: none !important;
        padding: 15px 10px !important;
    }
    .modern-table td:first-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .modern-table td:last-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    .modern-table thead th {
        border: none !important;
        background-color: transparent !important;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
</style>
