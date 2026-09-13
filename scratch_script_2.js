
        function togglePortalSidebar(action) {
            const isDesktop = window.innerWidth >= 1024;
            const sidebar = document.getElementById('portalSidebar');
            const backdrop = document.getElementById('portalSidebarBackdrop');

            if (isDesktop) {
                // Desktop collapse / expand
                if (action === false) {
                    document.body.classList.add('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', '1');
                } else if (action === true) {
                    document.body.classList.remove('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', '0');
                } else {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', isCollapsed ? '1' : '0');
                }
            } else {
                // Mobile off-canvas drawer
                if (action === true) {
                    if (sidebar) sidebar.classList.add('sidebar-mobile-open');
                    if (backdrop) backdrop.classList.add('active');
                    document.body.classList.add('overflow-hidden');
                } else if (action === false) {
                    if (sidebar) sidebar.classList.remove('sidebar-mobile-open');
                    if (backdrop) backdrop.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    const isOpen = sidebar ? sidebar.classList.toggle('sidebar-mobile-open') : false;
                    if (backdrop) {
                        if (isOpen) {
                            backdrop.classList.add('active');
                            document.body.classList.add('overflow-hidden');
                        } else {
                            backdrop.classList.remove('active');
                            document.body.classList.remove('overflow-hidden');
                        }
                    }
                }
            }
        }

        // Restore desktop collapsed state on initial load
        (function() {
            try {
                if (window.innerWidth >= 1024 && localStorage.getItem('portal_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        })();

        // Universal Backdrop Click & ESC Key Handling
        document.addEventListener('DOMContentLoaded', function () {
            // Clicking directly on the modal backdrop overlay closes the modal
            document.addEventListener('click', function (e) {
                if (e.target && e.target.classList && e.target.classList.contains('elite-modal')) {
                    window.closeModal(e.target.id);
                }
            });

            // ESC key closes topmost open modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' || e.key === 'Esc') {
                    const openModals = document.querySelectorAll('.elite-modal.active:not(.hidden)');
                    if (openModals.length > 0) {
                        const topModal = openModals[openModals.length - 1];
                        if (topModal && topModal.id) {
                            window.closeModal(topModal.id);
                        }
                    }
                }
            });
        });
    