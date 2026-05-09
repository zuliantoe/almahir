<style>
    .small-box {
        border-radius: 15px;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        color: #fff;
    }
    .small-box > .inner {
        padding: 20px;
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box p {
        font-size: 1rem;
        margin-bottom: 0;
    }
    .small-box .icon {
        color: rgba(0,0,0,.15);
        z-index: 0;
    }
    .small-box .icon > i {
        font-size: 70px;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: transform .3s linear;
    }
    .small-box:hover .icon > i {
        transform: scale(1.1);
    }
    .bg-info { background-color: #17a2b8 !important; }
    .bg-success { background-color: #28a745 !important; }
    .bg-warning { background-color: #ffc107 !important; color: #1f2d3d !important; }
    .bg-danger { background-color: #dc3545 !important; }
    .bg-secondary { background-color: #6c757d !important; }
    .bg-purple { background-color: #6f42c1 !important; }
    
    /* Responsive adjustment for small box icons */
    @media (max-width: 991.98px) {
        .small-box h3 { font-size: 1.8rem; }
        .small-box .icon > i { font-size: 50px; }
    }
</style>
