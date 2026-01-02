<nav class="navbar">
    <div class="nav-left">
        <span class="brand">📜 BacoLOG</span>
    </div>
    <div class="nav-right">
        <a href="index.php">Dashboard</a>
        <a href="viewer.php">Viewer</a>
    </div>
</nav>

<style>
.navbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 16px;
    background:#020617;
    border-bottom:1px solid #1e293b;
}
.brand {
    font-weight:700;
    color:#93c5fd;
}
.nav-right a {
    margin-left:16px;
    color:#e5e7eb;
    text-decoration:none;
    font-size:14px;
}
.nav-right a:hover {
    color:#93c5fd;
}

/* Mobile */
@media(max-width:768px){
    .nav-right a { margin-left:10px; font-size:13px; }
}
</style>