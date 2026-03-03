<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id   = $_SESSION['user_id'];
$username  = $_SESSION['username'] ?? 'User';
$role      = $_SESSION['role'] ?? 'user';

// If role not in session (old sessions), fetch from admin/users
if (!isset($_SESSION['role'])) {
    $ch = curl_init("http://127.0.0.1:8000/admin/users");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $r = curl_exec($ch); curl_close($ch);
    $users = json_decode($r, true) ?: [];
    foreach ($users as $u) {
        if ($u['id'] === $user_id) { $role = $u['role']; break; }
    }
}

// Fetch files server-side
$ch = curl_init("http://127.0.0.1:8000/files/$user_id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch); curl_close($ch);
$files = json_decode($r, true) ?: [];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LearnHub Dashboard</title>
<script>(function(){const t=localStorage.getItem('theme')||'light';document.documentElement.setAttribute('data-theme',t);})();</script>
<style>
:root{
  --font-family-base:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
  --color-bg-primary:#f8f9fa;--color-bg-secondary:#ffffff;--color-bg-surface:#ffffff;--color-bg-hover:#f1f3f5;
  --color-text-primary:#1a1a1a;--color-text-secondary:#6c757d;--color-text-muted:#adb5bd;
  --color-primary:#0d6efd;--color-primary-hover:#0b5ed7;--color-primary-active:#0a58ca;
  --color-border:#dee2e6;--color-border-light:#e9ecef;
  --color-success:#198754;--color-warning:#ffc107;--color-danger:#dc3545;--color-info:#0dcaf0;
  --shadow-sm:0 1px 3px rgba(0,0,0,.05);--shadow-md:0 4px 6px rgba(0,0,0,.07);--shadow-lg:0 10px 15px rgba(0,0,0,.1);
}
[data-theme="dark"]{
  --color-bg-primary:#0f172a;--color-bg-secondary:#1e293b;--color-bg-surface:#1e293b;--color-bg-hover:#334155;
  --color-text-primary:#f1f5f9;--color-text-secondary:#cbd5e1;--color-text-muted:#94a3b8;
  --color-primary:#38bdf8;--color-primary-hover:#0ea5e9;--color-primary-active:#0284c7;
  --color-border:#334155;--color-border-light:#475569;
  --color-success:#10b981;--color-warning:#f59e0b;--color-danger:#ef4444;--color-info:#06b6d4;
  --shadow-sm:0 1px 3px rgba(0,0,0,.3);--shadow-md:0 4px 6px rgba(0,0,0,.4);--shadow-lg:0 10px 15px rgba(0,0,0,.5);
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:var(--font-family-base);background:var(--color-bg-primary);color:var(--color-text-primary);line-height:1.6;overflow-x:hidden;}
.app-container{display:flex;height:100vh;}
/* Sidebar */
.sidebar{width:260px;background:var(--color-bg-secondary);border-right:1px solid var(--color-border);display:flex;flex-direction:column;flex-shrink:0;}
.sidebar-header{padding:1.5rem;border-bottom:1px solid var(--color-border);}
.logo{font-size:1.4rem;font-weight:700;color:var(--color-primary);display:flex;align-items:center;gap:.5rem;}
.logo-icon{width:32px;height:32px;background:linear-gradient(135deg,var(--color-primary),var(--color-primary-hover));border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.9rem;}
.sidebar-nav{flex:1;padding:1rem 0;overflow-y:auto;}
.nav-section{margin-bottom:1.5rem;}
.nav-section-title{padding:.5rem 1.5rem;font-size:.75rem;font-weight:600;text-transform:uppercase;color:var(--color-text-muted);letter-spacing:.05em;}
.nav-item{display:flex;align-items:center;padding:.75rem 1.5rem;color:var(--color-text-secondary);text-decoration:none;cursor:pointer;border-left:3px solid transparent;transition:all .2s;}
.nav-item:hover{background:var(--color-bg-hover);color:var(--color-text-primary);}
.nav-item.active{background:var(--color-bg-hover);color:var(--color-primary);border-left-color:var(--color-primary);font-weight:500;}
.nav-icon{margin-right:.75rem;font-size:1.1rem;}
.sidebar-footer{padding:1rem;border-top:1px solid var(--color-border);}
.theme-toggle,.account-btn{display:flex;align-items:center;width:100%;padding:.65rem 1rem;background:transparent;border:1px solid var(--color-border);border-radius:8px;color:var(--color-text-primary);cursor:pointer;transition:all .2s;font-size:.875rem;margin-bottom:.5rem;}
.theme-toggle:hover,.account-btn:hover{background:var(--color-bg-hover);border-color:var(--color-primary);}
/* Main */
.main-content{flex:1;overflow-y:auto;padding:2rem;}
.view-content{display:none;}
.view-content.active{display:block;}
.content-header{margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;}
.content-header h1{font-size:1.8rem;font-weight:700;}
.content-header p{color:var(--color-text-secondary);}
.content-header-left h1{font-size:1.8rem;font-weight:700;margin-bottom:.25rem;}
/* Cards/Widgets */
.widget{background:var(--color-bg-surface);border:1px solid var(--color-border);border-radius:12px;padding:1.5rem;box-shadow:var(--shadow-sm);margin-bottom:1.5rem;}
.widget-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;}
.widget-title{font-size:1.05rem;font-weight:600;display:flex;align-items:center;gap:.5rem;}
.dashboard-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:1.5rem;}
/* Buttons */
.btn{padding:.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:.875rem;font-weight:500;transition:all .2s;display:inline-flex;align-items:center;gap:.4rem;}
.btn-primary{background:var(--color-primary);color:#fff;}
.btn-primary:hover{background:var(--color-primary-hover);}
.btn-success{background:var(--color-success);color:#fff;}
.btn-success:hover{filter:brightness(1.1);}
.btn-danger{background:var(--color-danger);color:#fff;}
.btn-danger:hover{filter:brightness(1.1);}
.btn-secondary{background:var(--color-bg-hover);color:var(--color-text-primary);border:1px solid var(--color-border);}
.btn-secondary:hover{background:var(--color-border);}
.btn-sm{padding:.3rem .65rem;font-size:.8rem;}
.btn-icon{padding:.4rem;background:transparent;border:1px solid var(--color-border);border-radius:6px;cursor:pointer;color:var(--color-text-secondary);transition:all .2s;font-size:.9rem;}
.btn-icon:hover{background:var(--color-bg-hover);}
.btn-icon.danger:hover{color:var(--color-danger);border-color:var(--color-danger);}
/* Forms */
.form-row{display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-end;}
.form-group{display:flex;flex-direction:column;gap:.25rem;flex:1;min-width:120px;}
.form-group label{font-size:.8rem;font-weight:500;color:var(--color-text-secondary);}
.form-input{padding:.5rem .75rem;border:1px solid var(--color-border);border-radius:6px;background:var(--color-bg-primary);color:var(--color-text-primary);font-size:.875rem;}
.form-input:focus{outline:none;border-color:var(--color-primary);box-shadow:0 0 0 2px rgba(13,110,253,.15);}
select.form-input option{background:var(--color-bg-secondary);}
/* Toast */
#toast{position:fixed;bottom:1.5rem;right:1.5rem;padding:.75rem 1.25rem;border-radius:8px;color:#fff;font-weight:500;opacity:0;transform:translateY(10px);transition:all .3s;z-index:9999;max-width:320px;}
#toast.show{opacity:1;transform:translateY(0);}
#toast.success{background:var(--color-success);}
#toast.error{background:var(--color-danger);}
#toast.info{background:var(--color-primary);}
/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;}
.modal-overlay.open{display:flex;}
.modal{background:var(--color-bg-surface);border-radius:12px;box-shadow:var(--shadow-lg);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;}
.modal-header{display:flex;justify-content:space-between;align-items:center;padding:1.25rem 1.5rem;border-bottom:1px solid var(--color-border);}
.modal-header h2{font-size:1.1rem;font-weight:600;}
.modal-close{background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--color-text-muted);line-height:1;}
.modal-close:hover{color:var(--color-text-primary);}
.modal-body{padding:1.5rem;}
.modal-section{margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--color-border-light);}
.modal-section:last-child{margin-bottom:0;padding-bottom:0;border-bottom:none;}
.modal-section h3{font-size:.95rem;font-weight:600;margin-bottom:.75rem;color:var(--color-text-secondary);}
/* Day Tabs */
.day-tabs{display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;}
.day-tab{padding:.5rem 1rem;border:1px solid var(--color-border);border-radius:8px;cursor:pointer;font-size:.875rem;font-weight:500;background:var(--color-bg-hover);color:var(--color-text-secondary);transition:all .2s;}
.day-tab:hover{border-color:var(--color-primary);color:var(--color-primary);}
.day-tab.active{background:var(--color-primary);color:#fff;border-color:var(--color-primary);}
.day-tab.today{border-color:var(--color-success);}
.day-tab.today:not(.active){color:var(--color-success);}
/* Period rows */
.period-row{display:flex;gap:1rem;padding:.875rem 1rem;background:var(--color-bg-hover);border-radius:8px;margin-bottom:.5rem;align-items:flex-start;}
.period-num{background:var(--color-primary);color:#fff;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0;margin-top:.1rem;}
.period-time{font-size:.8rem;color:var(--color-text-muted);white-space:nowrap;min-width:110px;margin-top:.25rem;}
.period-subject{font-weight:600;font-size:.95rem;}
.period-body{flex:1;}
.hw-list{margin-top:.5rem;}
.hw-item{display:flex;align-items:center;gap:.5rem;padding:.3rem 0;font-size:.85rem;}
.hw-item.done .hw-text{text-decoration:line-through;color:var(--color-text-muted);}
.hw-checkbox{width:16px;height:16px;cursor:pointer;accent-color:var(--color-success);}
.hw-add-form{display:flex;gap:.5rem;margin-top:.5rem;}
.hw-add-form input{flex:1;padding:.35rem .6rem;border:1px solid var(--color-border);border-radius:6px;background:var(--color-bg-primary);color:var(--color-text-primary);font-size:.82rem;}
/* Edit grid */
.edit-mode-toggle{margin-bottom:1rem;}
.timetable-edit-grid{overflow-x:auto;margin-bottom:1.5rem;}
.timetable-edit-grid table{width:100%;border-collapse:collapse;min-width:500px;}
.timetable-edit-grid th{padding:.5rem .75rem;background:var(--color-bg-hover);border:1px solid var(--color-border);font-size:.85rem;font-weight:600;text-align:center;}
.timetable-edit-grid td{border:1px solid var(--color-border);padding:.25rem;}
.timetable-edit-grid .period-label{background:var(--color-bg-hover);padding:.5rem .75rem;font-size:.82rem;font-weight:600;text-align:center;white-space:nowrap;}
.grid-input{width:100%;padding:.4rem .5rem;border:none;background:transparent;color:var(--color-text-primary);font-size:.85rem;}
.grid-input:focus{outline:none;background:var(--color-bg-hover);}
.periods-editor{margin-bottom:1.5rem;}
.periods-editor h3{font-size:.95rem;font-weight:600;margin-bottom:.75rem;}
.period-edit-row{display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;flex-wrap:wrap;}
.period-edit-row .period-label-sm{font-size:.85rem;font-weight:600;min-width:55px;}
/* Subject cards (grades) */
.subject-card{background:var(--color-bg-surface);border:1px solid var(--color-border);border-radius:10px;margin-bottom:1rem;overflow:hidden;}
.subject-card-header{display:flex;justify-content:space-between;align-items:center;padding:.875rem 1.25rem;background:var(--color-bg-hover);border-bottom:1px solid var(--color-border);}
.subject-card-name{font-weight:700;font-size:1.05rem;}
.subject-avg{font-size:1.3rem;font-weight:800;padding:.2rem .6rem;border-radius:6px;color:#fff;}
.subject-avg.good{background:var(--color-success);}
.subject-avg.warn{background:var(--color-warning);color:#333;}
.subject-avg.bad{background:var(--color-danger);}
.subject-card-body{padding:1rem 1.25rem;}
.grade-entry{display:flex;align-items:center;gap:.5rem;padding:.4rem 0;border-bottom:1px solid var(--color-border-light);font-size:.875rem;}
.grade-entry:last-of-type{border-bottom:none;}
.grade-type-badge{padding:.15rem .45rem;border-radius:4px;font-size:.75rem;font-weight:600;color:#fff;}
.grade-type-badge.written{background:var(--color-primary);}
.grade-type-badge.oral{background:var(--color-success);}
.grade-val{font-weight:700;font-size:1rem;min-width:40px;}
.grade-desc{color:var(--color-text-secondary);flex:1;}
.grade-date{color:var(--color-text-muted);font-size:.78rem;white-space:nowrap;}
.weight-editor{margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--color-border-light);}
.weight-row{display:flex;align-items:center;gap:.75rem;font-size:.82rem;flex-wrap:wrap;}
.weight-row label{min-width:80px;color:var(--color-text-secondary);}
.weight-row input[type=range]{flex:1;min-width:80px;accent-color:var(--color-primary);}
.weight-val{min-width:36px;font-weight:600;}
/* System toggle */
.system-toggle{display:flex;gap:.5rem;margin-bottom:1rem;}
.system-btn{padding:.4rem .9rem;border:1px solid var(--color-border);border-radius:6px;cursor:pointer;background:transparent;color:var(--color-text-secondary);font-size:.875rem;font-weight:500;transition:all .2s;}
.system-btn.active{background:var(--color-primary);color:#fff;border-color:var(--color-primary);}
/* Todos */
.filter-tabs{display:flex;gap:.5rem;margin-bottom:1rem;}
.filter-tab{padding:.4rem .9rem;border:1px solid var(--color-border);border-radius:20px;cursor:pointer;background:transparent;color:var(--color-text-secondary);font-size:.82rem;transition:all .2s;}
.filter-tab.active{background:var(--color-primary);color:#fff;border-color:var(--color-primary);}
.todo-item{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;background:var(--color-bg-hover);border-radius:8px;margin-bottom:.5rem;}
.todo-item.done{opacity:.6;}
.todo-checkbox{width:18px;height:18px;cursor:pointer;accent-color:var(--color-success);flex-shrink:0;}
.todo-title{font-weight:500;flex:1;}
.todo-title.done{text-decoration:line-through;color:var(--color-text-muted);}
.todo-subject-tag{padding:.15rem .5rem;border-radius:12px;background:var(--color-bg-surface);border:1px solid var(--color-border);font-size:.75rem;white-space:nowrap;}
.todo-due{font-size:.78rem;color:var(--color-text-muted);white-space:nowrap;}
.priority-badge{padding:.15rem .5rem;border-radius:4px;font-size:.72rem;font-weight:700;letter-spacing:.03em;white-space:nowrap;}
.priority-badge.high{background:rgba(220,53,69,.15);color:var(--color-danger);}
.priority-badge.medium{background:rgba(255,193,7,.2);color:#997700;}
.priority-badge.low{background:rgba(13,202,240,.15);color:var(--color-info);}
[data-theme=dark] .priority-badge.medium{color:var(--color-warning);}
/* Flashcards */
.decks-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;}
.deck-card{background:var(--color-bg-surface);border:1px solid var(--color-border);border-radius:10px;overflow:hidden;transition:all .2s;}
.deck-card:hover{box-shadow:var(--shadow-md);transform:translateY(-2px);}
.deck-card-top{height:70px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;}
.deck-card-body{padding:1rem;}
.deck-card-name{font-weight:600;margin-bottom:.25rem;}
.deck-card-count{font-size:.82rem;color:var(--color-text-muted);margin-bottom:.75rem;}
.deck-card-actions{display:flex;gap:.5rem;}
.card-list-item{display:flex;align-items:center;gap:.75rem;padding:.65rem 1rem;background:var(--color-bg-hover);border-radius:8px;margin-bottom:.4rem;font-size:.875rem;}
.card-list-front{flex:1;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
/* Learning mode */
.learning-overlay{display:none;position:fixed;inset:0;background:var(--color-bg-primary);z-index:500;padding:2rem;align-items:center;justify-content:center;flex-direction:column;}
.learning-overlay.open{display:flex;}
.learning-top{width:100%;max-width:600px;display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;}
.learning-progress-bar{height:6px;background:var(--color-border);border-radius:3px;flex:1;margin:0 1rem;overflow:hidden;}
.learning-progress-fill{height:100%;background:var(--color-success);border-radius:3px;transition:width .3s;}
.learning-card{perspective:1000px;width:100%;max-width:600px;height:300px;cursor:pointer;margin-bottom:1.5rem;}
.learning-card-inner{position:relative;width:100%;height:100%;transition:transform .6s;transform-style:preserve-3d;}
.learning-card.flipped .learning-card-inner{transform:rotateY(180deg);}
.learning-face{position:absolute;inset:0;backface-visibility:hidden;border-radius:16px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;text-align:center;}
.learning-face.front{background:var(--color-bg-surface);border:2px solid var(--color-border);box-shadow:var(--shadow-md);}
.learning-face.back{background:linear-gradient(135deg,var(--color-primary),var(--color-primary-hover));color:#fff;transform:rotateY(180deg);}
.learning-face .hint{font-size:.8rem;color:var(--color-text-muted);margin-bottom:.75rem;}
.learning-face.back .hint{color:rgba(255,255,255,.7);}
.learning-face .card-text{font-size:1.2rem;font-weight:500;line-height:1.6;}
.learning-actions{display:flex;gap:1rem;}
.learning-actions .btn{min-width:140px;justify-content:center;font-size:1rem;padding:.75rem 1.5rem;}
/* Admin */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem;}
.stat-card{background:var(--color-bg-surface);border:1px solid var(--color-border);border-radius:10px;padding:1.25rem;text-align:center;}
.stat-value{font-size:2rem;font-weight:800;color:var(--color-primary);margin-bottom:.25rem;}
.stat-label{font-size:.82rem;color:var(--color-text-secondary);}
.admin-table-wrap{overflow-x:auto;}
.admin-table{width:100%;border-collapse:collapse;font-size:.875rem;}
.admin-table th{padding:.65rem .875rem;background:var(--color-bg-hover);border-bottom:2px solid var(--color-border);text-align:left;font-weight:600;}
.admin-table td{padding:.65rem .875rem;border-bottom:1px solid var(--color-border-light);}
.admin-table tr:hover td{background:var(--color-bg-hover);}
.role-badge{padding:.2rem .55rem;border-radius:12px;font-size:.75rem;font-weight:600;}
.role-badge.admin{background:rgba(220,53,69,.15);color:var(--color-danger);}
.role-badge.user{background:rgba(13,110,253,.1);color:var(--color-primary);}
/* Flashcard quick preview */
.mini-card{perspective:800px;height:160px;cursor:pointer;margin-bottom:.75rem;}
.mini-card-inner{position:relative;width:100%;height:100%;transition:transform .5s;transform-style:preserve-3d;}
.mini-card.flipped .mini-card-inner{transform:rotateY(180deg);}
.mini-face{position:absolute;inset:0;backface-visibility:hidden;border-radius:10px;display:flex;align-items:center;justify-content:center;padding:1rem;text-align:center;font-size:.9rem;}
.mini-face.front{background:var(--color-bg-hover);}
.mini-face.back{background:linear-gradient(135deg,var(--color-primary),var(--color-primary-hover));color:#fff;transform:rotateY(180deg);}
/* Overview small widgets */
.overview-timetable-item{display:flex;gap:.75rem;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--color-border-light);}
.overview-timetable-item:last-child{border-bottom:none;}
.overview-time{font-size:.8rem;color:var(--color-text-muted);min-width:95px;}
.overview-subject{font-weight:500;}
.ov-grade-item{display:flex;justify-content:space-between;align-items:center;padding:.4rem 0;border-bottom:1px solid var(--color-border-light);font-size:.875rem;}
.ov-grade-item:last-child{border-bottom:none;}
.ov-grade-val{font-weight:700;padding:.15rem .5rem;border-radius:4px;color:#fff;font-size:.85rem;}
/* Misc utility */
.empty-state{text-align:center;padding:2rem;color:var(--color-text-muted);}
.empty-state .empty-icon{font-size:2.5rem;margin-bottom:.5rem;}
.divider{border:none;border-top:1px solid var(--color-border);margin:1rem 0;}
.danger-zone{border:1px solid var(--color-danger);border-radius:8px;padding:1rem;}
.danger-zone h4{color:var(--color-danger);margin-bottom:.5rem;font-size:.9rem;}
::-webkit-scrollbar{width:6px;height:6px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:var(--color-border);border-radius:3px;}
.add-form-toggle{cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;padding:.4rem .85rem;border:1px dashed var(--color-border);border-radius:6px;font-size:.875rem;color:var(--color-text-secondary);background:transparent;transition:all .2s;}
.add-form-toggle:hover{border-color:var(--color-primary);color:var(--color-primary);}
.add-form-box{display:none;padding:1rem;background:var(--color-bg-hover);border-radius:8px;margin-top:.75rem;}
.add-form-box.open{display:block;}
.back-btn{display:inline-flex;align-items:center;gap:.4rem;color:var(--color-text-secondary);cursor:pointer;font-size:.9rem;margin-bottom:1rem;background:none;border:none;}
.back-btn:hover{color:var(--color-primary);}
@media(max-width:768px){.sidebar{position:fixed;left:-260px;z-index:100;height:100vh;transition:transform .3s;}.sidebar.open{transform:translateX(260px);}.main-content{padding:1rem;}.dashboard-grid{grid-template-columns:1fr;}.decks-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));}}
</style>
</head>
<body>
<div class="app-container">

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="logo"><div class="logo-icon">LH</div><span>LearnHub</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-section-title">Dashboard</div>
      <a class="nav-item active" data-view="overview"><span class="nav-icon">📊</span>Übersicht</a>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Module</div>
      <a class="nav-item" data-view="timetable"><span class="nav-icon">📅</span>Stundenplan</a>
      <a class="nav-item" data-view="grades"><span class="nav-icon">📝</span>Noten</a>
      <a class="nav-item" data-view="todos"><span class="nav-icon">✅</span>To-Dos</a>
      <a class="nav-item" data-view="flashcards"><span class="nav-icon">🎴</span>Karteikarten</a>
      <a class="nav-item" data-view="files"><span class="nav-icon">📁</span>Dateien</a>
      <?php if ($role === 'admin'): ?>
      <a class="nav-item" data-view="admin"><span class="nav-icon">⚙️</span>Admin Panel</a>
      <?php endif; ?>
    </div>
  </nav>
  <div class="sidebar-footer">
    <button class="theme-toggle" id="themeToggle"><span id="themeIcon">🌙</span><span id="themeText" style="margin-left:.5rem">Dark Mode</span></button>
    <button class="account-btn" onclick="openAccount()"><span>👤</span><span style="margin-left:.5rem"><?php echo htmlspecialchars($username); ?></span></button>
    <button class="account-btn" onclick="logout()" style="color:var(--color-danger);border-color:var(--color-danger);margin-bottom:0;"><span>🚪</span><span style="margin-left:.5rem">Abmelden</span></button>
  </div>
</aside>

<!-- Main Content -->
<main class="main-content">

<!-- ================== OVERVIEW ================== -->
<div id="view-overview" class="view-content active">
  <div class="content-header"><div class="content-header-left"><h1>Willkommen, <?php echo htmlspecialchars($username); ?>! 👋</h1><p>Deine Lernübersicht für heute</p></div></div>
  <div class="dashboard-grid">
    <div class="widget">
      <div class="widget-header"><div class="widget-title">📅 Heute im Stundenplan</div><button class="btn btn-sm btn-secondary" onclick="switchView('timetable')">→</button></div>
      <div id="ov-timetable"><div class="empty-state"><div class="empty-icon">📅</div><p>Lade...</p></div></div>
    </div>
    <div class="widget">
      <div class="widget-header"><div class="widget-title">📝 Aktuelle Noten</div><button class="btn btn-sm btn-secondary" onclick="switchView('grades')">→</button></div>
      <div id="ov-grades"><div class="empty-state"><div class="empty-icon">📝</div><p>Lade...</p></div></div>
    </div>
    <div class="widget">
      <div class="widget-header"><div class="widget-title">✅ Offene Aufgaben</div><button class="btn btn-sm btn-secondary" onclick="switchView('todos')">→</button></div>
      <div id="ov-todos"><div class="empty-state"><div class="empty-icon">✅</div><p>Lade...</p></div></div>
    </div>
    <div class="widget">
      <div class="widget-header"><div class="widget-title">🎴 Schnell lernen</div><button class="btn btn-sm btn-secondary" onclick="switchView('flashcards')">→</button></div>
      <div id="ov-flashcard"><div class="empty-state"><div class="empty-icon">🎴</div><p>Kein Stapel vorhanden</p></div></div>
    </div>
    <div class="widget">
      <div class="widget-header"><div class="widget-title">📁 Zuletzt hochgeladen</div><button class="btn btn-sm btn-secondary" onclick="switchView('files')">→</button></div>
      <div id="ov-files"><?php
        $slice = array_slice($files, 0, 3);
        if (empty($slice)) { echo '<div class="empty-state"><div class="empty-icon">📁</div><p>Keine Dateien</p></div>'; }
        else { foreach ($slice as $f) { $icon = str_contains(strtolower($f['original_name']), '.pdf') ? '📄' : '📎'; echo '<div class="overview-timetable-item"><span>'.$icon.'</span><span class="overview-subject">'.htmlspecialchars($f['original_name']).'</span></div>'; } }
      ?></div>
    </div>
    <?php if ($role === 'admin'): ?>
    <div class="widget">
      <div class="widget-header"><div class="widget-title">⚙️ Admin Übersicht</div><button class="btn btn-sm btn-secondary" onclick="switchView('admin')">→</button></div>
      <div id="ov-admin" class="stats-grid"></div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ================== STUNDENPLAN ================== -->
<div id="view-timetable" class="view-content">
  <div class="content-header">
    <div class="content-header-left"><h1>📅 Stundenplan</h1><p>Deine Wochenübersicht</p></div>
    <div style="display:flex;gap:.5rem;align-items:center">
      <button class="btn btn-secondary" id="editToggleBtn" onclick="toggleEditMode()">✏️ Bearbeiten</button>
    </div>
  </div>
  <!-- VIEW MODE -->
  <div id="tt-view-mode">
    <div class="day-tabs" id="dayTabs"></div>
    <div class="widget" id="tt-day-content"><div class="empty-state"><div class="empty-icon">📅</div><p>Keine Stunden für diesen Tag</p></div></div>
  </div>
  <!-- EDIT MODE -->
  <div id="tt-edit-mode" style="display:none;">
    <div class="widget">
      <div class="periods-editor">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;"><h3>⏰ Stundenzeiten</h3><button class="btn btn-sm btn-primary" onclick="addPeriodRow()">+ Stunde hinzufügen</button></div>
        <div id="periods-list"></div>
      </div>
    </div>
    <div class="widget">
      <h3 style="margin-bottom:1rem;font-size:.95rem;font-weight:600;">📋 Stundenplan bearbeiten</h3>
      <div class="timetable-edit-grid"><div id="edit-grid-wrap"></div></div>
      <p style="font-size:.78rem;color:var(--color-text-muted);margin-top:.5rem;">Fach eingeben und Enter drücken oder Feld verlassen zum Speichern. Leer lassen zum Löschen.</p>
    </div>
  </div>
</div>

<!-- ================== NOTEN ================== -->
<div id="view-grades" class="view-content">
  <div class="content-header">
    <div class="content-header-left"><h1>📝 Noten</h1><p>Alle deine Fächer und Noten</p></div>
    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
      <div class="system-toggle" id="gradeSystemToggle">
        <button class="system-btn active" id="sysPoints" onclick="setGradeSystem('points')">Punkte (0–15)</button>
        <button class="system-btn" id="sysGrades" onclick="setGradeSystem('grades')">Noten (1–6)</button>
      </div>
      <button class="btn btn-primary" onclick="openAddGradeModal()">+ Note hinzufügen</button>
    </div>
  </div>
  <div id="grades-list"></div>
</div>

<!-- ================== TO-DOS ================== -->
<div id="view-todos" class="view-content">
  <div class="content-header">
    <div class="content-header-left"><h1>✅ To-Dos</h1><p>Verwalte deine Aufgaben</p></div>
    <button class="btn btn-primary" onclick="toggleAddTodo()">+ Neue Aufgabe</button>
  </div>
  <div class="widget" id="todo-add-box" style="display:none;margin-bottom:1rem;">
    <h3 style="margin-bottom:.75rem;font-size:.95rem;font-weight:600;">Neue Aufgabe</h3>
    <div class="form-row">
      <div class="form-group" style="flex:2;min-width:160px;"><label>Titel *</label><input class="form-input" id="todo-title" placeholder="Aufgabenbeschreibung..."></div>
      <div class="form-group"><label>Fach</label><input class="form-input" id="todo-subject" placeholder="z.B. Mathe"></div>
      <div class="form-group"><label>Fällig am</label><input type="date" class="form-input" id="todo-due"></div>
      <div class="form-group"><label>Priorität</label>
        <select class="form-input" id="todo-priority">
          <option value="low">Niedrig</option>
          <option value="medium" selected>Mittel</option>
          <option value="high">Hoch</option>
        </select>
      </div>
      <div class="form-group" style="justify-content:flex-end;min-width:auto;"><label>&nbsp;</label><button class="btn btn-primary" onclick="addTodo()">Hinzufügen</button></div>
    </div>
  </div>
  <div class="filter-tabs">
    <button class="filter-tab active" data-filter="all">Alle</button>
    <button class="filter-tab" data-filter="open">Offen</button>
    <button class="filter-tab" data-filter="done">Erledigt</button>
  </div>
  <div id="todos-list"></div>
</div>

<!-- ================== KARTEIKARTEN ================== -->
<div id="view-flashcards" class="view-content">
  <!-- Deck overview -->
  <div id="fc-decks-view">
    <div class="content-header">
      <div class="content-header-left"><h1>🎴 Karteikarten</h1><p>Lerne mit Karteikarten-Stapeln</p></div>
      <button class="btn btn-primary" onclick="openAddDeckModal()">+ Neuer Stapel</button>
    </div>
    <div class="decks-grid" id="decks-grid"></div>
  </div>
  <!-- Cards in deck -->
  <div id="fc-cards-view" style="display:none;">
    <button class="back-btn" onclick="showDecksView()">← Zurück zu Stapeln</button>
    <div class="content-header">
      <div class="content-header-left"><h1 id="fc-deck-title">Stapel</h1><p id="fc-deck-subtitle"></p></div>
      <button class="btn btn-success" onclick="startLearning()">▶ Lernen starten</button>
    </div>
    <div class="widget">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
        <h3 style="font-size:.95rem;font-weight:600;">Karten</h3>
        <button class="btn btn-sm btn-primary" onclick="toggleAddCardForm()">+ Neue Karte</button>
      </div>
      <div class="add-form-box" id="add-card-form">
        <div class="form-row">
          <div class="form-group" style="flex:1;min-width:160px;"><label>Vorderseite (Frage)</label><input class="form-input" id="card-front" placeholder="Frage..."></div>
          <div class="form-group" style="flex:1;min-width:160px;"><label>Rückseite (Antwort)</label><input class="form-input" id="card-back" placeholder="Antwort..."></div>
          <div class="form-group" style="min-width:auto;justify-content:flex-end;"><label>&nbsp;</label><button class="btn btn-primary" onclick="addCard()">Hinzufügen</button></div>
        </div>
      </div>
      <div id="cards-list" style="margin-top:.75rem;"></div>
    </div>
  </div>
</div>

<!-- Learning overlay -->
<div class="learning-overlay" id="learning-overlay">
  <div class="learning-top" style="max-width:600px;width:100%;">
    <button class="btn btn-secondary btn-sm" onclick="endLearning()">✕ Beenden</button>
    <div class="learning-progress-bar"><div class="learning-progress-fill" id="learning-fill" style="width:0%"></div></div>
    <span id="learning-counter" style="font-size:.85rem;color:var(--color-text-secondary);white-space:nowrap;min-width:80px;text-align:right;">0 / 0</span>
  </div>
  <div class="learning-card" id="learning-card" onclick="flipLearningCard()">
    <div class="learning-card-inner">
      <div class="learning-face front"><span class="hint">Klicken zum Umdrehen</span><div class="card-text" id="lc-front"></div></div>
      <div class="learning-face back"><span class="hint">Antwort</span><div class="card-text" id="lc-back"></div></div>
    </div>
  </div>
  <div class="learning-actions">
    <button class="btn btn-danger" onclick="markUnknown()">✕ Noch nicht</button>
    <button class="btn btn-success" onclick="markKnown()">✓ Kann ich</button>
  </div>
  <p style="margin-top:1rem;font-size:.82rem;color:var(--color-text-muted);" id="learning-done-msg" style="display:none;"></p>
</div>

<!-- ================== DATEIEN ================== -->
<div id="view-files" class="view-content">
  <div class="content-header"><div class="content-header-left"><h1>📁 Dateien</h1><p>Lernmaterialien hochladen und verwalten</p></div></div>
  <div class="widget" style="margin-bottom:1rem;">
    <h3 style="margin-bottom:.75rem;font-size:.95rem;font-weight:600;">Datei hochladen</h3>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group"><label>Fach</label><input type="text" name="subject" class="form-input" placeholder="z.B. Mathematik" required></div>
        <div class="form-group" style="flex:2;"><label>Datei (PDF, PNG, JPG, DOCX, TXT – max 5 MB)</label><input type="file" name="file" class="form-input" required accept=".pdf,.png,.jpg,.jpeg,.docx,.txt"></div>
        <div class="form-group" style="min-width:auto;justify-content:flex-end;"><label>&nbsp;</label><button type="submit" class="btn btn-primary">Hochladen</button></div>
      </div>
    </form>
  </div>
  <div class="widget">
    <?php if (empty($files)): ?>
    <div class="empty-state"><div class="empty-icon">📁</div><p>Noch keine Dateien hochgeladen</p></div>
    <?php else: ?>
    <?php foreach ($files as $f): $ext = strtolower(pathinfo($f['original_name'], PATHINFO_EXTENSION)); $icon = in_array($ext, ['jpg','jpeg','png']) ? '🖼️' : ($ext === 'pdf' ? '📄' : '📎'); ?>
    <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem .5rem;border-bottom:1px solid var(--color-border-light);">
      <span style="font-size:1.4rem;"><?= $icon ?></span>
      <div style="flex:1;"><div style="font-weight:500;"><?= htmlspecialchars($f['original_name']) ?></div><div style="font-size:.78rem;color:var(--color-text-muted);"><?= htmlspecialchars($f['subject']) ?></div></div>
      <a class="btn btn-sm btn-secondary" href="download.php?file_id=<?= $f['id'] ?>">⬇️</a>
      <a class="btn btn-sm btn-danger" href="delete.php?file_id=<?= $f['id'] ?>" onclick="return confirm('Datei löschen?')">🗑️</a>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- ================== ADMIN ================== -->
<?php if ($role === 'admin'): ?>
<div id="view-admin" class="view-content">
  <div class="content-header"><div class="content-header-left"><h1>⚙️ Admin Panel</h1><p>Systemverwaltung und Statistiken</p></div></div>
  <div class="stats-grid" id="admin-stats"></div>
  <div class="widget">
    <h3 style="margin-bottom:1rem;font-size:.95rem;font-weight:600;">👥 Benutzer</h3>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>Benutzername</th><th>E-Mail</th><th>Rolle</th><th>Erstellt</th><th>Aktionen</th></tr></thead>
        <tbody id="admin-users-tbody"></tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

</main><!-- /main-content -->
</div><!-- /app-container -->

<!-- ================== ACCOUNT MODAL ================== -->
<div class="modal-overlay" id="accountModal">
  <div class="modal">
    <div class="modal-header"><h2>👤 Account-Einstellungen</h2><button class="modal-close" onclick="closeAccount()">✕</button></div>
    <div class="modal-body">
      <div class="modal-section">
        <h3>Benutzername ändern</h3>
        <div class="form-row"><input class="form-input" id="acc-username" placeholder="Neuer Benutzername" style="flex:1;" value="<?= htmlspecialchars($username) ?>"><button class="btn btn-primary" onclick="changeUsername()">Ändern</button></div>
      </div>
      <div class="modal-section">
        <h3>Passwort ändern</h3>
        <div class="form-group" style="margin-bottom:.5rem;"><label>Altes Passwort</label><input type="password" class="form-input" id="acc-old-pw" placeholder="Aktuelles Passwort"></div>
        <div class="form-group" style="margin-bottom:.75rem;"><label>Neues Passwort</label><input type="password" class="form-input" id="acc-new-pw" placeholder="Neues Passwort"></div>
        <button class="btn btn-primary" onclick="changePassword()">Passwort ändern</button>
      </div>
      <div class="modal-section">
        <h3>Notensystem</h3>
        <p style="font-size:.82rem;color:var(--color-text-secondary);margin-bottom:.75rem;">Wähle wie Noten angezeigt werden.</p>
        <div class="system-toggle">
          <button class="system-btn" id="acc-sysPoints" onclick="setGradeSystemModal('points')">Punkte (0–15)</button>
          <button class="system-btn" id="acc-sysGrades" onclick="setGradeSystemModal('grades')">Noten (1–6)</button>
        </div>
      </div>
      <div class="modal-section">
        <div class="danger-zone">
          <h4>⚠️ Account löschen</h4>
          <p style="font-size:.82rem;margin-bottom:.75rem;">Diese Aktion kann nicht rückgängig gemacht werden. Alle Daten werden gelöscht.</p>
          <div class="form-row"><input type="password" class="form-input" id="acc-del-pw" placeholder="Passwort zur Bestätigung" style="flex:1;"><button class="btn btn-danger" onclick="deleteAccount()">Account löschen</button></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================== ADD GRADE MODAL ================== -->
<div class="modal-overlay" id="addGradeModal">
  <div class="modal">
    <div class="modal-header"><h2>📝 Note hinzufügen</h2><button class="modal-close" onclick="closeAddGradeModal()">✕</button></div>
    <div class="modal-body">
      <div class="form-group" style="margin-bottom:.75rem;"><label>Fach *</label><input class="form-input" id="ag-subject" placeholder="z.B. Mathematik"></div>
      <div class="form-row" style="margin-bottom:.75rem;">
        <div class="form-group"><label id="ag-val-label">Wert (0–15) *</label><input type="number" class="form-input" id="ag-value" min="0" max="15" step="0.5" placeholder="z.B. 13"></div>
        <div class="form-group"><label>Typ</label>
          <select class="form-input" id="ag-type"><option value="written">Schriftlich</option><option value="oral">Mündlich</option></select>
        </div>
      </div>
      <div class="form-group" style="margin-bottom:1rem;"><label>Beschreibung</label><input class="form-input" id="ag-desc" placeholder="z.B. Klausur, Hausaufgabe..."></div>
      <button class="btn btn-primary" onclick="submitAddGrade()" style="width:100%;">Note speichern</button>
    </div>
  </div>
</div>

<!-- ================== ADD DECK MODAL ================== -->
<div class="modal-overlay" id="addDeckModal">
  <div class="modal">
    <div class="modal-header"><h2>🎴 Neuer Stapel</h2><button class="modal-close" onclick="closeAddDeckModal()">✕</button></div>
    <div class="modal-body">
      <div class="form-group" style="margin-bottom:.75rem;"><label>Name *</label><input class="form-input" id="deck-name" placeholder="z.B. Mathematik Grundlagen"></div>
      <div class="form-group" style="margin-bottom:1rem;"><label>Farbe</label>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;" id="deck-colors">
          <?php foreach (['#0d6efd','#198754','#dc3545','#ffc107','#0dcaf0','#6f42c1','#fd7e14','#20c997'] as $c): ?>
          <div onclick="selectDeckColor('<?= $c ?>')" style="width:32px;height:32px;border-radius:50%;background:<?= $c ?>;cursor:pointer;border:3px solid transparent;" data-color="<?= $c ?>"></div>
          <?php endforeach; ?>
        </div>
      </div>
      <input type="hidden" id="deck-color" value="#0d6efd">
      <button class="btn btn-primary" onclick="submitAddDeck()" style="width:100%;">Stapel erstellen</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast"></div>

<script>
const API = 'http://127.0.0.1:8000';
const USER_ID = '<?php echo $user_id; ?>';
const USER_ROLE = '<?php echo $role; ?>';
let gradeSystem = 'points';
let currentDeckId = null;
let currentDeckName = '';
let learningCards = [], learningIdx = 0, learnedCount = 0;

// ── API helper ──────────────────────────────────────────────
async function api(method, path, body = null) {
  const opts = { method, headers: { 'Content-Type': 'application/json' } };
  if (body) opts.body = JSON.stringify(body);
  const r = await fetch(API + path, opts);
  const data = await r.json().catch(() => ({}));
  if (!r.ok) throw new Error(data.detail || 'Fehler');
  return data;
}

// ── Toast ────────────────────────────────────────────────────
function toast(msg, type = 'success') {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className = type + ' show';
  clearTimeout(el._t);
  el._t = setTimeout(() => el.classList.remove('show'), 3000);
}

// ── Theme ────────────────────────────────────────────────────
(function () {
  const root = document.documentElement;
  const btn = document.getElementById('themeToggle');
  const icon = document.getElementById('themeIcon');
  const text = document.getElementById('themeText');
  function apply(t) {
    root.setAttribute('data-theme', t);
    localStorage.setItem('theme', t);
    icon.textContent = t === 'dark' ? '☀️' : '🌙';
    text.textContent = t === 'dark' ? 'Light Mode' : 'Dark Mode';
  }
  apply(localStorage.getItem('theme') || 'light');
  btn.onclick = () => apply(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
})();

// ── Navigation ───────────────────────────────────────────────
function switchView(id) {
  document.querySelectorAll('.view-content').forEach(v => v.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const v = document.getElementById('view-' + id);
  if (v) v.classList.add('active');
  const n = document.querySelector(`.nav-item[data-view="${id}"]`);
  if (n) n.classList.add('active');
  onViewLoad(id);
}
document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', () => switchView(item.dataset.view));
});
function onViewLoad(id) {
  if (id === 'overview')   loadOverview();
  if (id === 'timetable')  { loadTimetable(); loadPeriods(); }
  if (id === 'grades')     { loadGrades(); loadGradeSettings(); }
  if (id === 'todos')      loadTodos();
  if (id === 'flashcards') loadDecks();
  if (id === 'admin')      { loadAdminStats(); loadAdminUsers(); }
}

// ── Logout ───────────────────────────────────────────────────
function logout() {
  window.location.href = 'index.php';
}

// ═══════════════════════════════════════════════════════════════
// OVERVIEW
// ═══════════════════════════════════════════════════════════════
async function loadOverview() {
  const days = ['sonntag','montag','dienstag','mittwoch','donnerstag','freitag','samstag'];
  const today = days[new Date().getDay()];
  // Today's timetable
  try {
    const [tt, periods] = await Promise.all([
      api('GET', `/timetable/${USER_ID}`),
      api('GET', `/periods/${USER_ID}`)
    ]);
    const todayEntries = tt.filter(e => e.day === today).sort((a,b)=>a.period_number-b.period_number);
    const pmap = {};
    periods.forEach(p => pmap[p.period_number] = p);
    const el = document.getElementById('ov-timetable');
    if (!todayEntries.length) {
      el.innerHTML = '<div class="empty-state"><div class="empty-icon">🎉</div><p>Keine Stunden heute</p></div>';
    } else {
      el.innerHTML = todayEntries.map(e => {
        const p = pmap[e.period_number];
        const timeStr = p ? `${p.start_time} – ${p.end_time}` : `${e.period_number}. Stunde`;
        return `<div class="overview-timetable-item"><span class="overview-time">${timeStr}</span><span class="overview-subject">${escHtml(e.subject)}</span></div>`;
      }).join('');
    }
  } catch(e) {}
  // Recent grades
  try {
    const grades = await api('GET', `/grades/${USER_ID}`);
    const el = document.getElementById('ov-grades');
    const recent = grades.slice(0, 4);
    if (!recent.length) {
      el.innerHTML = '<div class="empty-state"><div class="empty-icon">📝</div><p>Noch keine Noten</p></div>';
    } else {
      el.innerHTML = recent.map(g => {
        const display = gradeSystem === 'points' ? `${g.value} P` : g.value;
        const cls = gradeColorClass(g.value);
        return `<div class="ov-grade-item"><span>${escHtml(g.subject)}</span><span class="ov-grade-val ${cls}">${display}</span></div>`;
      }).join('');
    }
  } catch(e) {}
  // Open todos
  try {
    const todos = await api('GET', `/todos/${USER_ID}`);
    const open = todos.filter(t => !t.done).slice(0, 4);
    const el = document.getElementById('ov-todos');
    if (!open.length) {
      el.innerHTML = '<div class="empty-state"><div class="empty-icon">🎉</div><p>Alle Aufgaben erledigt!</p></div>';
    } else {
      el.innerHTML = open.map(t => `
        <div class="todo-item" style="margin-bottom:.4rem;">
          <input type="checkbox" class="todo-checkbox" onchange="toggleTodo('${t.id}',this.checked)">
          <span class="todo-title">${escHtml(t.title)}</span>
          <span class="priority-badge ${t.priority}">${priLabel(t.priority)}</span>
        </div>`).join('');
    }
  } catch(e) {}
  // Quick flashcard
  try {
    const decks = await api('GET', `/flashcard-decks/${USER_ID}`);
    if (decks.length) {
      const deck = decks[0];
      const cards = await api('GET', `/flashcards/${USER_ID}?deck_id=${deck.id}`);
      const el = document.getElementById('ov-flashcard');
      if (cards.length) {
        const c = cards[Math.floor(Math.random() * cards.length)];
        el.innerHTML = `<div class="mini-card" id="mini-card" onclick="this.classList.toggle('flipped')">
          <div class="mini-card-inner">
            <div class="mini-face front">${escHtml(c.front)}</div>
            <div class="mini-face back">${escHtml(c.back)}</div>
          </div></div>
          <p style="font-size:.78rem;color:var(--color-text-muted);text-align:center;margin-top:.25rem;">Klicken zum Umdrehen – ${escHtml(deck.name)}</p>`;
      }
    }
  } catch(e) {}
  // Admin stats
  if (USER_ROLE === 'admin') {
    try {
      const s = await api('GET', '/admin/stats');
      document.getElementById('ov-admin').innerHTML = [
        ['👥', s.total_users, 'User'], ['🎴', s.total_flashcards, 'Karteikarten'],
        ['✅', s.total_todos, 'Todos'], ['📝', s.total_grades, 'Noten'],
        ['📁', s.total_files, 'Dateien'], ['📖', s.total_homework, 'Hausaufgaben']
      ].map(([ic,v,l]) => `<div class="stat-card"><div class="stat-value">${v}</div><div class="stat-label">${ic} ${l}</div></div>`).join('');
    } catch(e) {}
  }
}

// ═══════════════════════════════════════════════════════════════
// STUNDENPLAN
// ═══════════════════════════════════════════════════════════════
const DAYS = ['montag','dienstag','mittwoch','donnerstag','freitag'];
const DAYS_LABEL = {montag:'Montag',dienstag:'Dienstag',mittwoch:'Mittwoch',donnerstag:'Donnerstag',freitag:'Freitag'};
let ttEntries = [], ttPeriods = [], ttHomework = [], ttCurrentDay = 'montag', ttEditMode = false;

async function loadTimetable() {
  try {
    [ttEntries, ttPeriods, ttHomework] = await Promise.all([
      api('GET', `/timetable/${USER_ID}`),
      api('GET', `/periods/${USER_ID}`),
      api('GET', `/homework/${USER_ID}`)
    ]);
  } catch(e) { ttEntries=[]; ttPeriods=[]; ttHomework=[]; }
  const days = ['sonntag','montag','dienstag','mittwoch','donnerstag','freitag','samstag'];
  const todayStr = days[new Date().getDay()];
  ttCurrentDay = DAYS.includes(todayStr) ? todayStr : 'montag';
  renderDayTabs();
  renderDayView(ttCurrentDay);
}
async function loadPeriods() {
  ttPeriods = await api('GET', `/periods/${USER_ID}`).catch(() => []);
  renderPeriodsEditor();
  renderEditGrid();
}

function renderDayTabs() {
  const days = ['sonntag','montag','dienstag','mittwoch','donnerstag','freitag','samstag'];
  const todayStr = days[new Date().getDay()];
  document.getElementById('dayTabs').innerHTML = DAYS.map(d => {
    const isToday = d === todayStr;
    const isActive = d === ttCurrentDay;
    return `<div class="day-tab${isActive?' active':''}${isToday?' today':''}" onclick="selectDay('${d}')">${DAYS_LABEL[d].slice(0,2)}</div>`;
  }).join('');
}

function selectDay(day) {
  ttCurrentDay = day;
  renderDayTabs();
  renderDayView(day);
}

function renderDayView(day) {
  const entries = ttEntries.filter(e => e.day === day).sort((a,b)=>a.period_number-b.period_number);
  const pmap = {};
  ttPeriods.forEach(p => pmap[p.period_number] = p);
  const hw = ttHomework.filter(h => h.day === day);
  const el = document.getElementById('tt-day-content');
  if (!entries.length) {
    el.innerHTML = '<div class="empty-state"><div class="empty-icon">🎉</div><p>Keine Stunden – freier Tag!</p></div>';
    return;
  }
  el.innerHTML = entries.map(e => {
    const p = pmap[e.period_number];
    const timeStr = p ? `${p.start_time} – ${p.end_time}` : `${e.period_number}. Stunde`;
    const subjectHw = hw.filter(h => h.subject.toLowerCase() === e.subject.toLowerCase());
    const hwHtml = subjectHw.map(h => `
      <div class="hw-item${h.done?' done':''}" id="hw-${h.id}">
        <input type="checkbox" class="hw-checkbox" ${h.done?'checked':''} onchange="toggleHw('${h.id}',this.checked)">
        <span class="hw-text">${escHtml(h.text)}</span>
        <button class="btn-icon danger" style="padding:.1rem .3rem;font-size:.8rem;" onclick="deleteHw('${h.id}')">✕</button>
      </div>`).join('');
    return `
      <div class="period-row">
        <div class="period-num">${e.period_number}</div>
        <div class="period-body">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div><div class="period-subject">${escHtml(e.subject)}</div><div class="period-time">${timeStr}</div></div>
          </div>
          <div class="hw-list">${hwHtml}</div>
          <div class="hw-add-form">
            <input id="hw-inp-${e.id}" placeholder="Hausaufgabe hinzufügen..." onkeydown="if(event.key==='Enter')addHw('${e.id}','${escAttr(e.subject)}','${day}')">
            <button class="btn btn-sm btn-primary" onclick="addHw('${e.id}','${escAttr(e.subject)}','${day}')">+</button>
          </div>
        </div>
      </div>`;
  }).join('');
}

async function addHw(entryId, subject, day) {
  const inp = document.getElementById('hw-inp-' + entryId);
  const text = inp.value.trim();
  if (!text) return;
  try {
    await api('POST', `/homework/${USER_ID}`, { subject, day, text, due_date: '' });
    inp.value = '';
    ttHomework = await api('GET', `/homework/${USER_ID}`);
    renderDayView(day);
    toast('Hausaufgabe gespeichert');
  } catch(e) { toast(e.message, 'error'); }
}

async function toggleHw(id, done) {
  try {
    await api('PUT', `/homework/${USER_ID}/${id}/toggle`, { done });
    ttHomework = await api('GET', `/homework/${USER_ID}`);
    renderDayView(ttCurrentDay);
  } catch(e) { toast(e.message, 'error'); }
}

async function deleteHw(id) {
  try {
    await api('DELETE', `/homework/${USER_ID}/${id}`);
    ttHomework = await api('GET', `/homework/${USER_ID}`);
    renderDayView(ttCurrentDay);
    toast('Hausaufgabe gelöscht');
  } catch(e) { toast(e.message, 'error'); }
}

function toggleEditMode() {
  ttEditMode = !ttEditMode;
  document.getElementById('tt-view-mode').style.display = ttEditMode ? 'none' : 'block';
  document.getElementById('tt-edit-mode').style.display = ttEditMode ? 'block' : 'none';
  document.getElementById('editToggleBtn').textContent = ttEditMode ? '👁️ Ansicht' : '✏️ Bearbeiten';
  if (ttEditMode) { renderPeriodsEditor(); renderEditGrid(); }
}

function renderPeriodsEditor() {
  const list = document.getElementById('periods-list');
  const sorted = [...ttPeriods].sort((a,b) => a.period_number - b.period_number);
  list.innerHTML = sorted.map(p => `
    <div class="period-edit-row" id="pered-${p.period_number}">
      <span class="period-label-sm">${p.period_number}. Std</span>
      <input class="form-input" style="width:100px;" id="ps-start-${p.period_number}" value="${p.start_time}" placeholder="07:50">
      <span style="color:var(--color-text-muted);">–</span>
      <input class="form-input" style="width:100px;" id="ps-end-${p.period_number}" value="${p.end_time}" placeholder="08:35">
      <button class="btn btn-sm btn-primary" onclick="savePeriod(${p.period_number})">💾</button>
      <button class="btn btn-sm btn-danger" onclick="deletePeriod(${p.period_number})">✕</button>
    </div>`).join('');
}

async function addPeriodRow() {
  const max = ttPeriods.reduce((m, p) => Math.max(m, p.period_number), 0);
  const num = max + 1;
  try {
    await api('PUT', `/periods/${USER_ID}`, { period_number: num, start_time: '', end_time: '' });
    ttPeriods = await api('GET', `/periods/${USER_ID}`);
    renderPeriodsEditor();
    renderEditGrid();
    toast('Stunde hinzugefügt');
  } catch(e) { toast(e.message, 'error'); }
}

async function savePeriod(num) {
  const s = document.getElementById(`ps-start-${num}`).value.trim();
  const e = document.getElementById(`ps-end-${num}`).value.trim();
  try {
    await api('PUT', `/periods/${USER_ID}`, { period_number: num, start_time: s, end_time: e });
    ttPeriods = await api('GET', `/periods/${USER_ID}`);
    toast('Stundenzeit gespeichert');
    renderDayView(ttCurrentDay);
  } catch(err) { toast(err.message, 'error'); }
}

async function deletePeriod(num) {
  try {
    await api('DELETE', `/periods/${USER_ID}/${num}`);
    ttPeriods = await api('GET', `/periods/${USER_ID}`);
    renderPeriodsEditor();
    renderEditGrid();
    toast('Stunde gelöscht');
  } catch(e) { toast(e.message, 'error'); }
}

function renderEditGrid() {
  const nums = [...new Set([...ttPeriods.map(p=>p.period_number), ...Array.from({length:8},(_,i)=>i+1)])].sort((a,b)=>a-b).slice(0,10);
  const ttMap = {};
  ttEntries.forEach(e => ttMap[e.day+'_'+e.period_number] = e);
  const wrap = document.getElementById('edit-grid-wrap');
  const headers = ['<th style="min-width:80px;">Std</th>', ...DAYS.map(d=>`<th>${DAYS_LABEL[d]}</th>`)].join('');
  const rows = nums.map(n => {
    const cells = DAYS.map(d => {
      const key = d+'_'+n;
      const ent = ttMap[key];
      const val = ent ? ent.subject : '';
      return `<td><input class="grid-input" data-day="${d}" data-period="${n}" value="${escAttr(val)}" onblur="saveGridCell(this)" onkeydown="if(event.key==='Enter'){this.blur();}"></td>`;
    }).join('');
    return `<tr><td class="period-label">${n}. Std</td>${cells}</tr>`;
  }).join('');
  wrap.innerHTML = `<table><thead><tr>${headers}</tr></thead><tbody>${rows}</tbody></table>`;
}

async function saveGridCell(inp) {
  const day = inp.dataset.day;
  const period = parseInt(inp.dataset.period);
  const subject = inp.value.trim();
  const key = day + '_' + period;
  const ttMap = {};
  ttEntries.forEach(e => ttMap[e.day+'_'+e.period_number] = e);
  const existing = ttMap[key];
  try {
    if (existing) {
      if (subject === '') {
        await api('DELETE', `/timetable/${USER_ID}/${existing.id}`);
      } else if (subject !== existing.subject) {
        await api('PUT', `/timetable/${USER_ID}/${existing.id}`, { day, period_number: period, start_time: existing.start_time||'', end_time: existing.end_time||'', subject });
      }
    } else if (subject !== '') {
      const p = ttPeriods.find(pp => pp.period_number === period);
      await api('POST', `/timetable/${USER_ID}`, { day, period_number: period, start_time: p?.start_time||'', end_time: p?.end_time||'', subject });
    }
    ttEntries = await api('GET', `/timetable/${USER_ID}`);
  } catch(e) { toast(e.message, 'error'); }
}

// ═══════════════════════════════════════════════════════════════
// NOTEN
// ═══════════════════════════════════════════════════════════════
let allGrades = [], subjectSettings = [];

async function loadGradeSettings() {
  try {
    const s = await api('GET', `/settings/${USER_ID}`);
    gradeSystem = s.grade_system || 'points';
    updateGradeSystemUI();
  } catch(e) {}
  try { subjectSettings = await api('GET', `/settings/${USER_ID}/subjects`); } catch(e) { subjectSettings = []; }
}

async function loadGrades() {
  try {
    allGrades = await api('GET', `/grades/${USER_ID}`);
    renderGrades();
  } catch(e) { allGrades = []; }
}

function updateGradeSystemUI() {
  ['Points','Grades'].forEach(s => {
    document.getElementById('sys'+s)?.classList.toggle('active', gradeSystem === s.toLowerCase());
    document.getElementById('acc-sys'+s)?.classList.toggle('active', gradeSystem === s.toLowerCase());
  });
  const lbl = document.getElementById('ag-val-label');
  if (lbl) lbl.textContent = gradeSystem === 'points' ? 'Wert (0–15) *' : 'Note (1–6) *';
}

async function setGradeSystem(sys) {
  gradeSystem = sys;
  try {
    await api('PUT', `/settings/${USER_ID}`, { grade_system: sys });
    updateGradeSystemUI();
    renderGrades();
    toast('Notensystem gespeichert');
  } catch(e) { toast(e.message, 'error'); }
}
async function setGradeSystemModal(sys) { await setGradeSystem(sys); }

function gradeColorClass(val) {
  if (gradeSystem === 'points') return val >= 12 ? 'good' : val >= 8 ? 'warn' : 'bad';
  return val <= 2 ? 'good' : val <= 4 ? 'warn' : 'bad';
}
function gradeDisplay(val) {
  return gradeSystem === 'points' ? `${val} P` : val;
}

function calcWeightedAvg(grades, subject) {
  const written = grades.filter(g => g.grade_type === 'written');
  const oral = grades.filter(g => g.grade_type === 'oral');
  const ss = subjectSettings.find(s => s.subject === subject) || { written_weight: 0.7, oral_weight: 0.3 };
  if (!written.length && !oral.length) return null;
  if (!written.length) return oral.reduce((s,g)=>s+g.value,0)/oral.length;
  if (!oral.length) return written.reduce((s,g)=>s+g.value,0)/written.length;
  const avgW = written.reduce((s,g)=>s+g.value,0)/written.length;
  const avgO = oral.reduce((s,g)=>s+g.value,0)/oral.length;
  return avgW * ss.written_weight + avgO * ss.oral_weight;
}

function renderGrades() {
  const el = document.getElementById('grades-list');
  if (!allGrades.length) {
    el.innerHTML = '<div class="empty-state"><div class="empty-icon">📝</div><p>Noch keine Noten eingetragen</p><p style="font-size:.85rem;margin-top:.5rem;">Klicke oben auf "+ Note hinzufügen"</p></div>';
    return;
  }
  const bySubject = {};
  allGrades.forEach(g => { if (!bySubject[g.subject]) bySubject[g.subject] = []; bySubject[g.subject].push(g); });
  el.innerHTML = Object.entries(bySubject).map(([subj, grades]) => {
    const avg = calcWeightedAvg(grades, subj);
    const avgStr = avg !== null ? gradeDisplay(Math.round(avg * 10) / 10) : '–';
    const avgClass = avg !== null ? gradeColorClass(avg) : 'good';
    const ss = subjectSettings.find(s => s.subject === subj) || { written_weight: 0.7, oral_weight: 0.3 };
    const wPct = Math.round(ss.written_weight * 100);
    const oPct = Math.round(ss.oral_weight * 100);
    const gradeItems = grades.map(g => {
      const typeLabel = g.grade_type === 'oral' ? 'Mündlich' : 'Schriftlich';
      const cls = g.grade_type === 'oral' ? 'oral' : 'written';
      const d = new Date(g.date).toLocaleDateString('de-DE',{day:'2-digit',month:'2-digit',year:'2-digit'});
      return `<div class="grade-entry">
        <span class="grade-type-badge ${cls}">${typeLabel}</span>
        <span class="grade-val">${gradeDisplay(g.value)}</span>
        <span class="grade-desc">${escHtml(g.description||'')}</span>
        <span class="grade-date">${d}</span>
        <button class="btn-icon danger" onclick="deleteGrade('${g.id}')">🗑️</button>
      </div>`;
    }).join('');
    return `<div class="subject-card">
      <div class="subject-card-header">
        <span class="subject-card-name">${escHtml(subj)}</span>
        <span class="subject-avg ${avgClass}">${avgStr}</span>
      </div>
      <div class="subject-card-body">
        ${gradeItems}
        <div class="weight-editor">
          <div class="weight-row">
            <label>Schriftlich</label>
            <input type="range" min="0" max="100" step="5" value="${wPct}" oninput="updateWeightSlider(this,'${escAttr(subj)}','written')">
            <span class="weight-val" id="ww-${escAttr(subj)}">${wPct}%</span>
          </div>
          <div class="weight-row">
            <label>Mündlich</label>
            <input type="range" min="0" max="100" step="5" value="${oPct}" oninput="updateWeightSlider(this,'${escAttr(subj)}','oral')">
            <span class="weight-val" id="ow-${escAttr(subj)}">${oPct}%</span>
          </div>
        </div>
      </div>
    </div>`;
  }).join('');
}

function updateWeightSlider(slider, subject, type) {
  const v = parseInt(slider.value);
  const other = type === 'written' ? 'oral' : 'written';
  const otherVal = 100 - v;
  const otherSlider = slider.parentElement.parentElement.querySelector(`input[oninput*="'${other}'"]`);
  if (otherSlider) otherSlider.value = otherVal;
  document.getElementById('ww-' + subject).textContent = (type === 'written' ? v : otherVal) + '%';
  document.getElementById('ow-' + subject).textContent = (type === 'oral' ? v : otherVal) + '%';
  clearTimeout(slider._st);
  slider._st = setTimeout(() => saveSubjectWeight(subject, type === 'written' ? v/100 : otherVal/100, type === 'oral' ? v/100 : otherVal/100), 600);
}

async function saveSubjectWeight(subject, written_weight, oral_weight) {
  try {
    await api('PUT', `/settings/${USER_ID}/subjects`, { subject, written_weight, oral_weight });
    subjectSettings = await api('GET', `/settings/${USER_ID}/subjects`);
    renderGrades();
  } catch(e) { toast(e.message, 'error'); }
}

function openAddGradeModal() {
  const lbl = document.getElementById('ag-val-label');
  if (lbl) lbl.textContent = gradeSystem === 'points' ? 'Wert (0–15) *' : 'Note (1–6) *';
  const inp = document.getElementById('ag-value');
  if (gradeSystem === 'points') { inp.min=0; inp.max=15; } else { inp.min=1; inp.max=6; }
  document.getElementById('addGradeModal').classList.add('open');
}
function closeAddGradeModal() { document.getElementById('addGradeModal').classList.remove('open'); }

async function submitAddGrade() {
  const subject = document.getElementById('ag-subject').value.trim();
  const value = parseFloat(document.getElementById('ag-value').value);
  const grade_type = document.getElementById('ag-type').value;
  const description = document.getElementById('ag-desc').value.trim();
  if (!subject || isNaN(value)) { toast('Bitte Fach und Wert angeben', 'error'); return; }
  try {
    await api('POST', `/grades/${USER_ID}`, { subject, value, description, grade_type });
    ['ag-subject','ag-value','ag-desc'].forEach(id => document.getElementById(id).value = '');
    closeAddGradeModal();
    allGrades = await api('GET', `/grades/${USER_ID}`);
    subjectSettings = await api('GET', `/settings/${USER_ID}/subjects`);
    renderGrades();
    toast('Note gespeichert');
  } catch(e) { toast(e.message, 'error'); }
}

async function deleteGrade(id) {
  if (!confirm('Note löschen?')) return;
  try {
    await api('DELETE', `/grades/${USER_ID}/${id}`);
    allGrades = allGrades.filter(g => g.id !== id);
    renderGrades();
    toast('Note gelöscht');
  } catch(e) { toast(e.message, 'error'); }
}

// ═══════════════════════════════════════════════════════════════
// TO-DOS
// ═══════════════════════════════════════════════════════════════
let allTodos = [], todoFilter = 'all';

async function loadTodos() {
  try {
    allTodos = await api('GET', `/todos/${USER_ID}`);
    renderTodos();
  } catch(e) { allTodos = []; }
}

document.querySelectorAll('.filter-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    todoFilter = tab.dataset.filter;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    renderTodos();
  });
});

function priLabel(p) { return {high:'HOCH',medium:'MITTEL',low:'NIEDRIG'}[p] || p; }

function renderTodos() {
  const el = document.getElementById('todos-list');
  let filtered = allTodos;
  if (todoFilter === 'open') filtered = allTodos.filter(t => !t.done);
  if (todoFilter === 'done') filtered = allTodos.filter(t => t.done);
  if (!filtered.length) {
    el.innerHTML = '<div class="empty-state"><div class="empty-icon">✅</div><p>' + (todoFilter === 'done' ? 'Keine erledigten Aufgaben' : 'Keine offenen Aufgaben') + '</p></div>';
    return;
  }
  const sorted = [...filtered].sort((a,b) => {
    const po = {high:3,medium:2,low:1};
    if (!a.done && b.done) return -1;
    if (a.done && !b.done) return 1;
    return (po[b.priority]||0) - (po[a.priority]||0);
  });
  el.innerHTML = sorted.map(t => `
    <div class="todo-item${t.done?' done':''}">
      <input type="checkbox" class="todo-checkbox" ${t.done?'checked':''} onchange="toggleTodo('${t.id}',this.checked)">
      <span class="todo-title${t.done?' done':''}">${escHtml(t.title)}</span>
      ${t.subject ? `<span class="todo-subject-tag">${escHtml(t.subject)}</span>` : ''}
      ${t.due_date ? `<span class="todo-due">📅 ${fmtDate(t.due_date)}</span>` : ''}
      <span class="priority-badge ${t.priority}">${priLabel(t.priority)}</span>
      <button class="btn-icon danger" onclick="deleteTodo('${t.id}')">🗑️</button>
    </div>`).join('');
}

function toggleAddTodo() {
  const box = document.getElementById('todo-add-box');
  box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

async function addTodo() {
  const title = document.getElementById('todo-title').value.trim();
  const subject = document.getElementById('todo-subject').value.trim();
  const due_date = document.getElementById('todo-due').value;
  const priority = document.getElementById('todo-priority').value;
  if (!title) { toast('Bitte Titel angeben', 'error'); return; }
  try {
    await api('POST', `/todos/${USER_ID}`, { title, subject, due_date, priority });
    ['todo-title','todo-subject','todo-due'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('todo-add-box').style.display = 'none';
    allTodos = await api('GET', `/todos/${USER_ID}`);
    renderTodos();
    toast('Aufgabe hinzugefügt');
  } catch(e) { toast(e.message, 'error'); }
}

async function toggleTodo(id, done) {
  try {
    await api('PUT', `/todos/${USER_ID}/${id}/toggle`, { done });
    const t = allTodos.find(t => t.id === id);
    if (t) t.done = done;
    renderTodos();
  } catch(e) { toast(e.message, 'error'); }
}

async function deleteTodo(id) {
  if (!confirm('Aufgabe löschen?')) return;
  try {
    await api('DELETE', `/todos/${USER_ID}/${id}`);
    allTodos = allTodos.filter(t => t.id !== id);
    renderTodos();
    toast('Aufgabe gelöscht');
  } catch(e) { toast(e.message, 'error'); }
}

// ═══════════════════════════════════════════════════════════════
// KARTEIKARTEN
// ═══════════════════════════════════════════════════════════════
let allDecks = [], currentDeckCards = [];

async function loadDecks() {
  try {
    allDecks = await api('GET', `/flashcard-decks/${USER_ID}`);
    renderDecks();
  } catch(e) { allDecks = []; renderDecks(); }
}

function renderDecks() {
  const el = document.getElementById('decks-grid');
  if (!allDecks.length) {
    el.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><div class="empty-icon">🎴</div><p>Noch keine Stapel – erstelle deinen ersten!</p></div>';
    return;
  }
  el.innerHTML = allDecks.map(d => `
    <div class="deck-card">
      <div class="deck-card-top" style="background:${d.color}">🎴</div>
      <div class="deck-card-body">
        <div class="deck-card-name">${escHtml(d.name)}</div>
        <div class="deck-card-count">${d.card_count || 0} Karten</div>
        <div class="deck-card-actions">
          <button class="btn btn-sm btn-primary" onclick="openDeck('${d.id}','${escAttr(d.name)}',${d.card_count||0})">Karten</button>
          <button class="btn btn-sm btn-success" onclick="openDeckAndLearn('${d.id}','${escAttr(d.name)}',${d.card_count||0})">Lernen</button>
          <button class="btn-icon danger" onclick="deleteDeck('${d.id}')">🗑️</button>
        </div>
      </div>
    </div>`).join('');
}

async function openDeck(deckId, deckName, count) {
  currentDeckId = deckId;
  currentDeckName = deckName;
  document.getElementById('fc-deck-title').textContent = deckName;
  document.getElementById('fc-deck-subtitle').textContent = `${count} Karten`;
  document.getElementById('fc-decks-view').style.display = 'none';
  document.getElementById('fc-cards-view').style.display = 'block';
  try {
    currentDeckCards = await api('GET', `/flashcards/${USER_ID}?deck_id=${deckId}`);
  } catch(e) { currentDeckCards = []; }
  renderCardsList();
}

async function openDeckAndLearn(deckId, deckName, count) {
  await openDeck(deckId, deckName, count);
  startLearning();
}

function showDecksView() {
  document.getElementById('fc-cards-view').style.display = 'none';
  document.getElementById('fc-decks-view').style.display = 'block';
  loadDecks();
}

function renderCardsList() {
  const el = document.getElementById('cards-list');
  if (!currentDeckCards.length) {
    el.innerHTML = '<div class="empty-state"><div class="empty-icon">🃏</div><p>Noch keine Karten – füge die erste hinzu!</p></div>';
    return;
  }
  el.innerHTML = currentDeckCards.map(c => `
    <div class="card-list-item">
      <span class="card-list-front">${escHtml(c.front)}</span>
      <span style="font-size:.78rem;color:var(--color-text-muted);">→ ${escHtml(c.back.slice(0,40))}${c.back.length>40?'…':''}</span>
      <button class="btn-icon danger" onclick="deleteCard('${c.id}')">🗑️</button>
    </div>`).join('');
}

function toggleAddCardForm() {
  const box = document.getElementById('add-card-form');
  box.classList.toggle('open');
}

async function addCard() {
  const front = document.getElementById('card-front').value.trim();
  const back = document.getElementById('card-back').value.trim();
  if (!front || !back) { toast('Vorder- und Rückseite ausfüllen', 'error'); return; }
  try {
    await api('POST', `/flashcards/${USER_ID}`, { subject: currentDeckName, front, back, public: false, deck_id: currentDeckId });
    document.getElementById('card-front').value = '';
    document.getElementById('card-back').value = '';
    document.getElementById('add-card-form').classList.remove('open');
    currentDeckCards = await api('GET', `/flashcards/${USER_ID}?deck_id=${currentDeckId}`);
    renderCardsList();
    toast('Karte hinzugefügt');
  } catch(e) { toast(e.message, 'error'); }
}

async function deleteCard(id) {
  if (!confirm('Karte löschen?')) return;
  try {
    await api('DELETE', `/flashcards/${USER_ID}/${id}`);
    currentDeckCards = currentDeckCards.filter(c => c.id !== id);
    renderCardsList();
    toast('Karte gelöscht');
  } catch(e) { toast(e.message, 'error'); }
}

async function deleteDeck(id) {
  if (!confirm('Stapel und alle Karten löschen?')) return;
  try {
    await api('DELETE', `/flashcard-decks/${USER_ID}/${id}`);
    allDecks = allDecks.filter(d => d.id !== id);
    renderDecks();
    toast('Stapel gelöscht');
  } catch(e) { toast(e.message, 'error'); }
}

// Learning mode
function startLearning() {
  if (!currentDeckCards.length) { toast('Keine Karten zum Lernen', 'error'); return; }
  learningCards = [...currentDeckCards].sort(() => Math.random() - 0.5);
  learningIdx = 0;
  learnedCount = 0;
  document.getElementById('learning-overlay').classList.add('open');
  showLearningCard();
}

function showLearningCard() {
  if (learningIdx >= learningCards.length) {
    document.getElementById('lc-front').textContent = '🎉 Session abgeschlossen!';
    document.getElementById('lc-back').textContent = `${learnedCount} von ${learningCards.length} gewusst`;
    document.getElementById('learning-fill').style.width = '100%';
    document.getElementById('learning-counter').textContent = `${learnedCount} / ${learningCards.length}`;
    return;
  }
  const c = learningCards[learningIdx];
  document.getElementById('lc-front').textContent = c.front;
  document.getElementById('lc-back').textContent = c.back;
  document.getElementById('learning-card').classList.remove('flipped');
  const pct = Math.round(learnedCount / learningCards.length * 100);
  document.getElementById('learning-fill').style.width = pct + '%';
  document.getElementById('learning-counter').textContent = `${learnedCount} / ${learningCards.length}`;
}

function flipLearningCard() {
  document.getElementById('learning-card').classList.toggle('flipped');
}

async function markKnown() {
  const c = learningCards[learningIdx];
  if (c) { try { await api('PUT', `/flashcards/${USER_ID}/${c.id}/known`, { known: true }); } catch(e) {} }
  learnedCount++;
  learningIdx++;
  showLearningCard();
}

async function markUnknown() {
  const c = learningCards[learningIdx];
  if (c) { try { await api('PUT', `/flashcards/${USER_ID}/${c.id}/known`, { known: false }); } catch(e) {} }
  learningIdx++;
  showLearningCard();
}

function endLearning() {
  document.getElementById('learning-overlay').classList.remove('open');
}

// Deck modal
let selectedDeckColor = '#0d6efd';
function openAddDeckModal() { document.getElementById('addDeckModal').classList.add('open'); }
function closeAddDeckModal() { document.getElementById('addDeckModal').classList.remove('open'); }
function selectDeckColor(c) {
  selectedDeckColor = c;
  document.getElementById('deck-color').value = c;
  document.querySelectorAll('#deck-colors div').forEach(d => {
    d.style.borderColor = d.dataset.color === c ? '#fff' : 'transparent';
  });
}
async function submitAddDeck() {
  const name = document.getElementById('deck-name').value.trim();
  const color = document.getElementById('deck-color').value || '#0d6efd';
  if (!name) { toast('Bitte Name eingeben', 'error'); return; }
  try {
    await api('POST', `/flashcard-decks/${USER_ID}`, { name, color });
    document.getElementById('deck-name').value = '';
    closeAddDeckModal();
    await loadDecks();
    toast('Stapel erstellt');
  } catch(e) { toast(e.message, 'error'); }
}

// ═══════════════════════════════════════════════════════════════
// ADMIN
// ═══════════════════════════════════════════════════════════════
async function loadAdminStats() {
  try {
    const s = await api('GET', '/admin/stats');
    document.getElementById('admin-stats').innerHTML = [
      ['👥', s.total_users, 'Benutzer'], ['🎴', s.total_flashcards, 'Karteikarten'],
      ['✅', s.total_todos, 'Todos'], ['📝', s.total_grades, 'Noten'],
      ['📁', s.total_files, 'Dateien'], ['📖', s.total_homework, 'Hausaufgaben']
    ].map(([ic,v,l]) => `<div class="stat-card"><div class="stat-value">${v}</div><div class="stat-label">${ic} ${l}</div></div>`).join('');
  } catch(e) {}
}

async function loadAdminUsers() {
  try {
    const users = await api('GET', '/admin/users');
    const tbody = document.getElementById('admin-users-tbody');
    tbody.innerHTML = users.map(u => {
      const isMe = u.id === USER_ID;
      const roleLabel = u.role === 'admin' ? '<span class="role-badge admin">Admin</span>' : '<span class="role-badge user">User</span>';
      const d = new Date(u.created_at).toLocaleDateString('de-DE');
      const toggleRole = u.role === 'admin' ? 'user' : 'admin';
      const toggleLabel = u.role === 'admin' ? '→ User' : '→ Admin';
      return `<tr>
        <td><strong>${escHtml(u.username)}</strong>${isMe?' (Du)':''}</td>
        <td>${escHtml(u.email||'')}</td>
        <td>${roleLabel}</td>
        <td>${d}</td>
        <td style="display:flex;gap:.4rem;flex-wrap:wrap;">
          ${!isMe ? `<button class="btn btn-sm btn-secondary" onclick="adminChangeRole('${u.id}','${toggleRole}')">${toggleLabel}</button>` : ''}
          ${!isMe ? `<button class="btn btn-sm btn-danger" onclick="adminDeleteUser('${u.id}','${escAttr(u.username)}')">🗑️</button>` : ''}
          ${isMe ? '<span style="color:var(--color-text-muted);font-size:.8rem;">–</span>' : ''}
        </td>
      </tr>`;
    }).join('');
  } catch(e) { toast(e.message, 'error'); }
}

async function adminChangeRole(userId, role) {
  try {
    await api('PUT', `/admin/users/${userId}/role`, { role });
    toast('Rolle geändert');
    loadAdminUsers();
  } catch(e) { toast(e.message, 'error'); }
}

async function adminDeleteUser(userId, name) {
  if (!confirm(`Benutzer "${name}" und alle Daten löschen?`)) return;
  try {
    await api('DELETE', `/admin/users/${userId}`);
    toast('Benutzer gelöscht');
    loadAdminUsers();
    loadAdminStats();
  } catch(e) { toast(e.message, 'error'); }
}

// ═══════════════════════════════════════════════════════════════
// ACCOUNT MODAL
// ═══════════════════════════════════════════════════════════════
async function openAccount() {
  document.getElementById('accountModal').classList.add('open');
  try {
    const s = await api('GET', `/settings/${USER_ID}`);
    gradeSystem = s.grade_system || 'points';
    updateGradeSystemUI();
  } catch(e) {}
}
function closeAccount() { document.getElementById('accountModal').classList.remove('open'); }

async function changeUsername() {
  const val = document.getElementById('acc-username').value.trim();
  if (!val) { toast('Benutzername eingeben', 'error'); return; }
  try {
    await api('PUT', `/auth/change-username/${USER_ID}`, { new_username: val });
    toast('Benutzername geändert');
  } catch(e) { toast(e.message, 'error'); }
}

async function changePassword() {
  const oldPw = document.getElementById('acc-old-pw').value;
  const newPw = document.getElementById('acc-new-pw').value;
  if (!oldPw || !newPw) { toast('Beide Felder ausfüllen', 'error'); return; }
  try {
    await api('PUT', `/auth/change-password/${USER_ID}`, { old_password: oldPw, new_password: newPw });
    document.getElementById('acc-old-pw').value = '';
    document.getElementById('acc-new-pw').value = '';
    toast('Passwort geändert');
  } catch(e) { toast(e.message, 'error'); }
}

async function deleteAccount() {
  const pw = document.getElementById('acc-del-pw').value;
  if (!pw) { toast('Passwort eingeben', 'error'); return; }
  if (!confirm('Account wirklich dauerhaft löschen? Alle Daten gehen verloren!')) return;
  try {
    await api('DELETE', `/auth/delete-account/${USER_ID}`, { password: pw });
    window.location.href = 'login.php';
  } catch(e) { toast(e.message, 'error'); }
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('open'); });
});

// ─── Utility ────────────────────────────────────────────────
function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(s) {
  return String(s).replace(/'/g,"\\'").replace(/"/g,'&quot;');
}
function fmtDate(s) {
  if (!s) return '';
  try { return new Date(s).toLocaleDateString('de-DE',{day:'2-digit',month:'2-digit',year:'2-digit'}); } catch(e) { return s; }
}

// ─── Init ────────────────────────────────────────────────────
(async function init() {
  try {
    const s = await api('GET', `/settings/${USER_ID}`);
    gradeSystem = s.grade_system || 'points';
    updateGradeSystemUI();
  } catch(e) {}
  loadOverview();
})();
</script>
</body>
</html>
