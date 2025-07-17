 
    function updateContent() {
        const hash = window.location.hash || '#/admin/welcome';
        const path = hash.substring(2); // 移除#/
        const iframe = document.getElementById('contentFrame');
        iframe.src = '/' + path; // 直接使用path作为src

        // 更新菜单高亮和子菜单展开
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if (href === hash) {
                link.classList.add('active');
                // 如果是子菜单项，展开父级菜单
                const parentSubMenu = link.closest('.sub-menu');
                if (parentSubMenu) {
                    parentSubMenu.classList.add('show');
                }
            }
        });
    }

    // 页面加载时初始化
    document.addEventListener('DOMContentLoaded', () => {
        updateContent();

        // 处理一级菜单点击（展开/收起或加载内容）
        document.querySelectorAll('.nav-link[data-has-submenu]').forEach(link => {
            link.addEventListener('click', function (e) {
                const hasSubmenu = this.getAttribute('data-has-submenu') === 'true';
                const submenu = this.nextElementSibling;
                if (hasSubmenu && submenu.classList.contains('sub-menu')) {
                    e.preventDefault(); // 阻止默认跳转
                    submenu.classList.toggle('show'); // 展开或收起
                }
            });
        });
 

        // 处理侧边栏收起/展开
        const toggleSidebarBtn = document.getElementById('toggleSidebar');
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');
        const navbar = document.querySelector('.navbar');

        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
            navbar.classList.toggle('collapsed');
            toggleSidebarBtn.classList.toggle('bi-list');
            toggleSidebarBtn.classList.toggle('bi-arrow-right');
        });
    });

    // 监听hash变化
    window.addEventListener('hashchange', updateContent); 

    