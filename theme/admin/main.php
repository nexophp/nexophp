<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理系统</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            overflow: hidden;
        }
        #sidebar {
            height: 100vh;
            background-color: #212529;
            color: white;
            transition: all 0.3s;
        }
        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            margin-bottom: 2px;
        }
        #sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        #sidebar .nav-link.active {
            color: white;
            background-color: #0d6efd;
        }
        #sidebar .nav-link i {
            margin-right: 8px;
        }
        .submenu {
            padding-left: 30px;
            background-color: rgba(0, 0, 0, 0.1);
        }
        .submenu .nav-link {
            padding-top: 5px;
            padding-bottom: 5px;
        }
        #content {
            height: 100vh;
            padding: 0;
        }
        #main-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .menu-collapse {
            cursor: pointer;
        }
        .menu-collapse[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
            transition: transform 0.3s;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- 侧边栏 -->
            <div id="sidebar" class="col-md-3 col-lg-2 d-md-block p-0">
                <div class="sidebar-header">
                    <h4 class="text-center">管理系统</h4>
                </div>
                <div class="nav flex-column px-2 pt-3">
                    <!-- 一级菜单 -->
                    <a href="#dashboard" class="nav-link active">
                        <i class="bi bi-speedometer2"></i> 控制台
                    </a>
                    
                    <a class="nav-link menu-collapse" data-bs-toggle="collapse" href="#systemMenu" role="button">
                        <i class="bi bi-gear"></i> 系统管理 <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <!-- 二级菜单 -->
                    <div class="collapse show submenu" id="systemMenu">
                        <a href="#system/user" class="nav-link">用户管理</a>
                        <a href="#system/role" class="nav-link">角色管理</a>
                        <a href="#system/permission" class="nav-link">权限管理</a>
                    </div>
                    
                    <a class="nav-link menu-collapse" data-bs-toggle="collapse" href="#contentMenu" role="button">
                        <i class="bi bi-file-earmark-text"></i> 内容管理 <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <div class="collapse submenu" id="contentMenu">
                        <a href="#content/article" class="nav-link">文章管理</a>
                        <a href="#content/category" class="nav-link">分类管理</a>
                        <a href="#content/tag" class="nav-link">标签管理</a>
                    </div>
                    
                    <a class="nav-link menu-collapse" data-bs-toggle="collapse" href="#statsMenu" role="button">
                        <i class="bi bi-graph-up"></i> 统计分析 <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <div class="collapse submenu" id="statsMenu">
                        <a href="#stats/visitor" class="nav-link">访问统计</a>
                        <a href="#stats/behavior" class="nav-link">行为分析</a>
                    </div>
                    
                    <a href="#settings" class="nav-link">
                        <i class="bi bi-tools"></i> 系统设置
                    </a>
                </div>
            </div>
            
            <!-- 主内容区 -->
            <div id="content" class="col-md-9 col-lg-10 ms-sm-auto">
                <iframe id="main-iframe" src="about:blank"></iframe>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // 路由配置
        const routes = {
            'dashboard': {
                title: '控制台',
                url: 'https://example.com/dashboard'
            },
            'system/user': {
                title: '用户管理',
                url: 'https://example.com/system/user'
            },
            'system/role': {
                title: '角色管理',
                url: 'https://example.com/system/role'
            },
            'system/permission': {
                title: '权限管理',
                url: 'https://example.com/system/permission'
            },
            'content/article': {
                title: '文章管理',
                url: 'https://example.com/content/article'
            },
            'content/category': {
                title: '分类管理',
                url: 'https://example.com/content/category'
            },
            'content/tag': {
                title: '标签管理',
                url: 'https://example.com/content/tag'
            },
            'stats/visitor': {
                title: '访问统计',
                url: 'https://example.com/stats/visitor'
            },
            'stats/behavior': {
                title: '行为分析',
                url: 'https://example.com/stats/behavior'
            },
            'settings': {
                title: '系统设置',
                url: 'https://example.com/settings'
            }
        };

        // 初始化路由
        function initRouter() {
            // 获取当前hash
            let currentHash = window.location.hash.substring(1);
            
            // 如果没有hash，使用默认路由
            if (!currentHash) {
                currentHash = 'dashboard';
                window.location.hash = currentHash;
            }
            
            // 导航到对应路由
            navigateTo(currentHash);
            
            // 监听hash变化
            window.addEventListener('hashchange', function() {
                const newHash = window.location.hash.substring(1);
                navigateTo(newHash);
            });
        }

        // 导航到指定路由
        function navigateTo(route) {
            // 更新菜单激活状态
            updateActiveMenu(route);
            
            // 查找路由配置
            const routeConfig = routes[route];
            
            if (routeConfig) {
                // 更新iframe
                document.getElementById('main-iframe').src = routeConfig.url;
                // 更新页面标题
                document.title = `${routeConfig.title} - 后台管理系统`;
            } else {
                // 路由不存在，显示404或默认页面
                document.getElementById('main-iframe').src = 'about:blank';
                document.title = '页面未找到 - 后台管理系统';
            }
        }

        // 更新菜单激活状态
        function updateActiveMenu(currentRoute) {
            // 移除所有active类
            document.querySelectorAll('#sidebar .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // 找到匹配的菜单项并激活
            const matchedLink = document.querySelector(`#sidebar a[href="#${currentRoute}"]`);
            if (matchedLink) {
                matchedLink.classList.add('active');
                
                // 确保父级菜单展开
                const parentMenu = matchedLink.closest('.submenu');
                if (parentMenu) {
                    const collapseId = parentMenu.id;
                    const collapseElement = document.querySelector(`a[data-bs-toggle="collapse"][href="#${collapseId}"]`);
                    if (collapseElement) {
                        new bootstrap.Collapse(parentMenu, { show: true });
                    }
                }
            }
        }

        // 页面加载完成后初始化路由
        document.addEventListener('DOMContentLoaded', initRouter);
    </script>
</body>
</html>