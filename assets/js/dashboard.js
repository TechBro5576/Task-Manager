/* ============================================
   DASHBOARD — JAVASCRIPT
   ============================================ */

(function () {
    'use strict';

    const CFG = window.TASKS_GO || {};
    const state = {
        counts: CFG.counts || { total: 0, todo: 0, done: 0, today: 0, overdue: 0 },
        filter: 'all',
        projectId: null,
        search: ''
    };

    /* ============================================
       1. SIDEBAR (mobile)
       ============================================ */
    const sidebar  = document.getElementById('sidebar');
    const backdrop = document.getElementById('backdrop');
    const menuBtn  = document.getElementById('menuBtn');
    const closeBtn = document.getElementById('sidebarClose');

    function openSidebar() {
        sidebar?.classList.add('is-open');
        backdrop?.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar?.classList.remove('is-open');
        backdrop?.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    menuBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    backdrop?.addEventListener('click', closeSidebar);

    /* ============================================
       2. MODALS
       ============================================ */
    const taskModal      = document.getElementById('taskModal');
    const taskForm       = document.getElementById('taskForm');
    const taskIdField    = document.getElementById('taskIdField');
    const taskModalTitle = document.getElementById('taskModalTitle');
    const taskSubmitBtn  = document.getElementById('taskSubmitBtn');
    const taskTitle      = document.getElementById('taskTitle');
    const taskNotes      = document.getElementById('taskNotes');
    const taskPriority   = document.getElementById('taskPriority');
    const taskDue        = document.getElementById('taskDue');
    const taskProject    = document.getElementById('taskProject');

    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (!sidebar?.classList.contains('is-open')) {
            document.body.style.overflow = '';
        }
    }

    function resetTaskForm() {
        taskForm.reset();
        taskIdField.value = '';
        taskModalTitle.textContent = 'New task';
        taskSubmitBtn.innerHTML = '<i class="bi bi-plus-lg"></i> <span>Add task</span>';
        taskForm.action = 'actions/add_task.php';
    }

    function openNewTaskModal() {
        resetTaskForm();
        openModal('taskModal');
        setTimeout(() => taskTitle.focus(), 60);
    }

    function openEditTaskModal(taskEl) {
        resetTaskForm();

        taskIdField.value  = taskEl.dataset.id;
        taskTitle.value    = taskEl.dataset.title || '';
        taskNotes.value    = taskEl.dataset.notes || '';
        taskPriority.value = taskEl.dataset.priority || 'medium';
        taskDue.value      = taskEl.dataset.due || '';
        taskProject.value  = taskEl.dataset.project || '';

        taskModalTitle.textContent = 'Edit task';
        taskSubmitBtn.innerHTML = '<i class="bi bi-check-lg"></i> <span>Save changes</span>';
        taskForm.action = 'actions/edit_task.php';

        openModal('taskModal');
        setTimeout(() => taskTitle.focus(), 60);
    }

    document.getElementById('openTaskModal')?.addEventListener('click', openNewTaskModal);
    document.getElementById('openTaskModalEmpty')?.addEventListener('click', openNewTaskModal);
    document.getElementById('openProjectModal')?.addEventListener('click', function () {
        openModal('projectModal');
    });

    document.querySelectorAll('[data-close]').forEach(function (el) {
        el.addEventListener('click', function () {
            closeModal(this.dataset.close);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            document.querySelectorAll('.dash-modal.is-open').forEach(function (m) {
                closeModal(m.id);
            });
        }
    });

    /* ============================================
       3. FLASH MESSAGES
       ============================================ */
    function dismissFlash(el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        el.style.transition = 'opacity .3s ease, transform .3s ease';
        setTimeout(() => el.remove(), 300);
    }

    document.querySelectorAll('.dash-flash').forEach(function (flash) {
        const close = flash.querySelector('.dash-flash-close');
        close?.addEventListener('click', () => dismissFlash(flash));
        setTimeout(() => dismissFlash(flash), 4500);
    });

    function showFlash(message, type) {
        const content = document.querySelector('.dash-content');
        if (!content) return;

        document.querySelectorAll('.dash-flash').forEach(f => f.remove());

        const el = document.createElement('div');
        el.className = 'dash-flash' + (type === 'error' ? ' dash-flash-error' : '');
        el.setAttribute('role', 'alert');
        el.innerHTML = `
            <i class="bi bi-${type === 'error' ? 'exclamation-triangle-fill' : 'check-circle-fill'}"></i>
            <span></span>
            <button type="button" class="dash-flash-close" aria-label="Dismiss">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
        el.querySelector('span').textContent = message;
        content.prepend(el);

        el.querySelector('.dash-flash-close').addEventListener('click', () => dismissFlash(el));
        setTimeout(() => dismissFlash(el), 4500);
    }

    /* ============================================
       4. HELPER — today as YYYY-MM-DD
       ============================================ */
    function todayStr() {
        const d = new Date();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + m + '-' + day;
    }

    /* ============================================
       5. RECALCULATE COUNTS FROM THE DOM
       The bulletproof way — never trusts deltas
       ============================================ */
    function recalculateCountsFromDOM() {
        const today = todayStr();
        let total = 0, todo = 0, done = 0, todayCount = 0, overdue = 0;

        document.querySelectorAll('.dash-task').forEach(function (task) {
            if (task.classList.contains('is-removing')) return;
            total++;

            const status = task.dataset.status;
            const due    = task.dataset.due;

            if (status === 'done') {
                done++;
            } else {
                todo++;
                if (due === today) {
                    todayCount++;
                } else if (due && due < today) {
                    overdue++;
                }
            }
        });

        state.counts.total   = total;
        state.counts.todo    = todo;
        state.counts.done    = done;
        state.counts.today   = todayCount;
        state.counts.overdue = overdue;
    }

    /* ============================================
       6. STATS + BADGES — animate updates live
       ============================================ */
    function animateNumber(el, to, duration) {
        const from = parseInt(el.textContent, 10) || 0;
        if (from === to) return;

        const start = performance.now();

        function step(now) {
            const elapsed = now - start;
            const t = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - t, 3);
            el.textContent = Math.round(from + (to - from) * eased);
            if (t < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = to;
            }
        }

        requestAnimationFrame(step);
    }

    function refreshStats() {
        const c = state.counts;

        ['total', 'todo', 'done', 'today', 'overdue'].forEach(function (k) {
            if (typeof c[k] !== 'number' || c[k] < 0 || isNaN(c[k])) c[k] = 0;
        });

        const progress = c.total > 0 ? Math.round((c.done / c.total) * 100) : 0;

        // Progress percentage
        document.querySelectorAll('[data-stat="progress"]').forEach(function (el) {
            animateNumber(el, progress, 500);
        });

        // Progress bar (if present)
        document.querySelectorAll('[data-stat="progressBar"]').forEach(function (el) {
            el.style.width = progress + '%';
        });

        // Progress ring — the live one
        document.querySelectorAll('[data-stat="progressRing"]').forEach(function (el) {
            el.setAttribute('stroke-dasharray', progress + ', 100');
        });

        // Celebration state at 100%
        document.querySelectorAll('.dash-stat-hero').forEach(function (el) {
            el.classList.toggle('is-complete', progress === 100 && c.total > 0);
        });

        // Stat values
        document.querySelectorAll('[data-stat="total"]').forEach(function (el) {
            if (el.closest('.dash-stat-hero')) {
                animateNumber(el, c.total, 400);
            } else {
                el.textContent = c.total;
            }
        });
        document.querySelectorAll('[data-stat="todo"]').forEach(el => el.textContent = c.todo);
        document.querySelectorAll('[data-stat="done"]').forEach(function (el) {
            if (el.closest('.dash-stat-hero')) {
                animateNumber(el, c.done, 400);
            } else {
                el.textContent = c.done;
            }
        });

        // Sidebar badges
        document.querySelectorAll('[data-count="todo"]').forEach(el => el.textContent = c.todo);
        document.querySelectorAll('[data-count="done"]').forEach(function (el) {
            el.textContent = c.done;
            el.style.display = c.done === 0 ? 'none' : '';
        });
        document.querySelectorAll('[data-count="today"]').forEach(function (el) {
            el.textContent = c.today;
            el.style.display = c.today === 0 ? 'none' : '';
        });
        document.querySelectorAll('[data-count="overdue"]').forEach(function (el) {
            el.textContent = c.overdue;
            el.style.display = c.overdue === 0 ? 'none' : '';
        });

        // Greeting subtitle
        const greetingSub = document.querySelector('.dash-greeting p');
        if (greetingSub) {
            if (c.todo === 0) {
                greetingSub.innerHTML = "You're all caught up. Nothing on the list.";
            } else if (c.overdue > 0) {
                greetingSub.innerHTML = 'You have <strong>' + c.overdue + '</strong> overdue and <strong>' + c.todo + '</strong> to do.';
            } else {
                greetingSub.innerHTML = 'You have <strong>' + c.todo + '</strong> task' + (c.todo === 1 ? '' : 's') + ' on your list.';
            }
        }

        // Greeting overdue pill
        const greetingMeta = document.querySelector('.dash-greeting-meta');
        if (greetingMeta) {
            const existingAlert = greetingMeta.querySelector('.dash-greeting-alert');
            if (c.overdue > 0) {
                if (existingAlert) {
                    existingAlert.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> ' + c.overdue + ' overdue';
                } else {
                    const alertEl = document.createElement('span');
                    alertEl.className = 'dash-greeting-alert';
                    alertEl.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> ' + c.overdue + ' overdue';
                    greetingMeta.appendChild(alertEl);
                }
            } else if (existingAlert) {
                existingAlert.remove();
            }
        }
    }

    /* ============================================
       7. VIEW FILTERS (sidebar)
       ============================================ */
    const taskListEl   = document.getElementById('taskList');
    const emptyState   = document.getElementById('emptyState');
    const emptyTitle   = document.getElementById('emptyTitle');
    const emptyText    = document.getElementById('emptyText');
    const tasksHeading = document.getElementById('tasksHeading');
    const tasksCountEl = document.getElementById('tasksCount');

    function taskMatchesFilter(taskEl) {
        const status = taskEl.dataset.status;
        const due    = taskEl.dataset.due;
        const projId = taskEl.dataset.project;
        const today  = todayStr();

        if (state.search) {
            const text = taskEl.textContent.toLowerCase();
            if (!text.includes(state.search)) return false;
        }

        switch (state.filter) {
            case 'today':
                return status === 'todo' && due === today;
            case 'overdue':
                return status === 'todo' && due && due < today;
            case 'completed':
                return status === 'done';
            case 'project':
                return String(projId) === String(state.projectId);
            case 'all':
            default:
                return true;
        }
    }

    function applyFilter() {
        if (!taskListEl) return;

        const tasks = taskListEl.querySelectorAll('.dash-task');
        let visible = 0;

        tasks.forEach(function (task) {
            const show = taskMatchesFilter(task);
            task.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const headings = {
            all:       'Your tasks',
            today:     'Due today',
            overdue:   'Overdue tasks',
            completed: 'Completed tasks',
            project:   'Project tasks'
        };
        if (tasksHeading) tasksHeading.textContent = headings[state.filter] || 'Your tasks';
        if (tasksCountEl) tasksCountEl.textContent = visible + ' shown';

        if (emptyState) {
            if (visible === 0) {
                emptyState.style.display = '';
                if (taskListEl) taskListEl.style.display = 'none';

                const messages = {
                    all:       ['No tasks yet', 'Add your first task and start getting things done.'],
                    today:     ['Nothing due today', 'Enjoy the clear schedule.'],
                    overdue:   ['Nothing overdue', 'You\'re on top of it.'],
                    completed: ['Nothing completed yet', 'Check off a task to see it here.'],
                    project:   ['No tasks in this project', 'Add one to get started.']
                };
                const [t, txt] = messages[state.filter] || messages.all;
                if (emptyTitle) emptyTitle.textContent = t;
                if (emptyText)  emptyText.textContent  = txt;
            } else {
                emptyState.style.display = 'none';
                if (taskListEl) taskListEl.style.display = '';
            }
        }
    }

    document.querySelectorAll('.dash-nav-link[data-view]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.dash-nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            state.filter = this.dataset.view;
            state.projectId = null;
            applyFilter();
            closeSidebar();
        });
    });

    document.querySelectorAll('.dash-nav-link[data-project]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.dash-nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            state.filter = 'project';
            state.projectId = this.dataset.project;
            applyFilter();
            closeSidebar();
        });
    });

    /* ============================================
       8. SEARCH
       ============================================ */
    const search = document.getElementById('taskSearch');
    if (search) {
        search.addEventListener('input', function () {
            state.search = this.value.trim().toLowerCase();
            applyFilter();
        });
    }

    /* ============================================
       9. TOGGLE TASK — live ring update
       ============================================ */
    document.querySelectorAll('[data-action="toggle"]').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id   = this.dataset.id;
            const task = this.closest('.dash-task');
            if (!task) return;

            const wasDone  = task.classList.contains('is-done');
            const nextDone = !wasDone;
            const icon     = this.querySelector('i');

            // Optimistic UI
            task.classList.toggle('is-done', nextDone);
            task.dataset.status = nextDone ? 'done' : 'todo';
            if (icon) icon.className = nextDone ? 'bi bi-check-lg' : 'bi';

            // Recalculate EVERYTHING from DOM — bulletproof
            recalculateCountsFromDOM();
            refreshStats();
            applyFilter();

            if (typeof refreshPanelIfOpen === 'function') {
                refreshPanelIfOpen(id);
            }

            try {
                const res = await fetch('api/tasks.php', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': CFG.csrfToken
                    },
                    body: JSON.stringify({ id: id, status: nextDone ? 'done' : 'todo' })
                });

                const data = await res.json().catch(() => null);

                if (!res.ok || !data || data.ok !== true) {
                    throw new Error((data && data.error) || 'Request failed');
                }
            } catch (err) {
                // Revert
                task.classList.toggle('is-done', wasDone);
                task.dataset.status = wasDone ? 'done' : 'todo';
                if (icon) icon.className = wasDone ? 'bi bi-check-lg' : 'bi';

                recalculateCountsFromDOM();
                refreshStats();
                applyFilter();

                if (typeof refreshPanelIfOpen === 'function') {
                    refreshPanelIfOpen(id);
                }

                showFlash('Could not update the task.', 'error');
            }
        });
    });

    /* ============================================
       10. DELETE TASK — inline confirm
       ============================================ */
    document.querySelectorAll('[data-action="delete"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const task = this.closest('.dash-task');
            if (!task) return;
            task.classList.add('is-confirming');
        });
    });

    document.querySelectorAll('[data-action="cancel-delete"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            this.closest('.dash-task')?.classList.remove('is-confirming');
        });
    });

    document.querySelectorAll('[data-action="confirm-delete"]').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id   = this.dataset.id;
            const task = this.closest('.dash-task');
            if (!task) return;

            task.classList.remove('is-confirming');
            task.classList.add('is-removing');

            try {
                const res = await fetch('api/tasks.php?id=' + encodeURIComponent(id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-Token': CFG.csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json().catch(() => null);

                if (!res.ok || !data || data.ok !== true) {
                    throw new Error((data && data.error) || 'Request failed');
                }

                // Remove from DOM, then recount — bulletproof
                setTimeout(function () {
                    task.remove();
                    recalculateCountsFromDOM();
                    refreshStats();
                    applyFilter();
                }, 200);

                showFlash('Task deleted.', 'success');
            } catch (err) {
                task.classList.remove('is-removing');
                showFlash('Could not delete the task.', 'error');
            }
        });
    });

    /* ============================================
       11. TASK FORM — AJAX
       ============================================ */
    if (taskForm) {
        taskForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const original = taskSubmitBtn.innerHTML;
            taskSubmitBtn.disabled = true;
            taskSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch(taskForm.action, {
                    method: 'POST',
                    body: new FormData(taskForm),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!res.ok) throw new Error('Request failed');

                closeModal('taskModal');
                showFlash(taskIdField.value ? 'Task updated. Refreshing…' : 'Task added. Refreshing…', 'success');
                setTimeout(() => window.location.reload(), 500);
            } catch (err) {
                showFlash('Could not save the task.', 'error');
                taskSubmitBtn.disabled = false;
                taskSubmitBtn.innerHTML = original;
            }
        });
    }

    /* ============================================
       12. PROJECT FORM — AJAX
       ============================================ */
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = projectForm.querySelector('button[type="submit"]');
            const original  = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch(projectForm.action, {
                    method: 'POST',
                    body: new FormData(projectForm),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!res.ok) throw new Error('Request failed');

                closeModal('projectModal');
                showFlash('Project created. Refreshing…', 'success');
                setTimeout(() => window.location.reload(), 500);
            } catch (err) {
                showFlash('Could not create the project.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = original;
            }
        });
    }

    /* ============================================
       13. TASK DETAIL PANEL
       ============================================ */
    const taskPanel        = document.getElementById('taskPanel');
    const panelTitle       = document.getElementById('taskPanelTitle');
    const panelMeta        = document.getElementById('panelMeta');
    const panelNotes       = document.getElementById('panelNotes');
    const panelNotesSec    = document.getElementById('panelNotesSection');
    const panelDetails     = document.getElementById('panelDetails');
    const panelStatus      = document.getElementById('panelStatus');
    const panelToggle      = document.getElementById('panelToggle');
    const panelEditBtn     = document.getElementById('panelEditBtn');
    const panelEditBtnFoot = document.getElementById('panelEditBtnFoot');
    const panelDeleteBtn   = document.getElementById('panelDeleteBtn');

    let currentTaskEl = null;

    function formatDate(str) {
        if (!str) return '—';
        const d = new Date(str + 'T00:00:00');
        return d.toLocaleDateString(undefined, {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function formatDateShort(str) {
        if (!str) return '—';
        const d = new Date(str + 'T00:00:00');
        return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
    }

    function getPriorityLabel(p) {
        return { high: 'High', medium: 'Medium', low: 'Low' }[p] || 'Medium';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderPanel(taskEl) {
        currentTaskEl = taskEl;

        const title    = taskEl.dataset.title || '';
        const notes    = taskEl.dataset.notes || '';
        const priority = taskEl.dataset.priority || 'medium';
        const status   = taskEl.dataset.status || 'todo';
        const due      = taskEl.dataset.due || '';

        panelTitle.textContent = title;
        panelTitle.classList.toggle('is-done', status === 'done');

        panelStatus.textContent = status === 'done' ? 'Completed' : 'To do';
        panelToggle.classList.toggle('is-done', status === 'done');

        panelMeta.innerHTML = '';

        if (priority === 'high') {
            panelMeta.insertAdjacentHTML('beforeend',
                '<span class="dash-tag dash-tag-high"><i class="bi bi-flag-fill"></i> High</span>');
        } else if (priority === 'low') {
            panelMeta.insertAdjacentHTML('beforeend',
                '<span class="dash-tag dash-tag-low"><i class="bi bi-flag"></i> Low</span>');
        }

        const projectTag = taskEl.querySelector('.dash-task-meta .dash-tag[style]');
        if (projectTag) {
            panelMeta.appendChild(projectTag.cloneNode(true));
        }

        const today = todayStr();
        if (due) {
            let tagClass = 'dash-tag';
            let label = formatDateShort(due);
            if (status !== 'done') {
                if (due === today) { tagClass += ' dash-tag-warn'; label = 'Today'; }
                else if (due < today) { tagClass += ' dash-tag-danger'; label = 'Overdue'; }
            }
            panelMeta.insertAdjacentHTML('beforeend',
                '<span class="' + tagClass + '"><i class="bi bi-calendar3"></i> ' + escapeHtml(label) + '</span>');
        }

        if (notes && notes.trim()) {
            panelNotes.textContent = notes;
            panelNotes.classList.remove('is-empty');
            panelNotesSec.style.display = '';
        } else {
            panelNotes.textContent = 'No notes for this task.';
            panelNotes.classList.add('is-empty');
        }

        panelDetails.innerHTML = `
            <div>
                <dt><i class="bi bi-flag"></i> Priority</dt>
                <dd>${escapeHtml(getPriorityLabel(priority))}</dd>
            </div>
            <div>
                <dt><i class="bi bi-calendar3"></i> Due date</dt>
                <dd>${escapeHtml(due ? formatDate(due) : 'No due date')}</dd>
            </div>
            <div>
                <dt><i class="bi bi-list-check"></i> Status</dt>
                <dd>${escapeHtml(status === 'done' ? 'Completed' : 'In progress')}</dd>
            </div>
        `;
    }

    function refreshPanelIfOpen(taskId) {
        if (!taskPanel?.classList.contains('is-open')) return;
        if (!currentTaskEl || currentTaskEl.dataset.id !== String(taskId)) return;
        renderPanel(currentTaskEl);
    }

    function openPanel(taskEl) {
        if (!taskEl) return;
        renderPanel(taskEl);
        taskPanel.classList.add('is-open');
        taskPanel.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closePanel() {
        taskPanel.classList.remove('is-open');
        taskPanel.setAttribute('aria-hidden', 'true');
        if (!sidebar?.classList.contains('is-open')) {
            document.body.style.overflow = '';
        }
        currentTaskEl = null;
    }

    document.querySelectorAll('[data-action="view"]').forEach(function (el) {
        el.addEventListener('click', function () {
            openPanel(this.closest('.dash-task'));
        });
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openPanel(this.closest('.dash-task'));
            }
        });
    });

    taskPanel?.querySelectorAll('[data-close="taskPanel"]').forEach(function (el) {
        el.addEventListener('click', closePanel);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && taskPanel?.classList.contains('is-open')) {
            closePanel();
        }
    });

    panelToggle?.addEventListener('click', async function () {
        if (!currentTaskEl) return;
        const cardCheck = currentTaskEl.querySelector('[data-action="toggle"]');
        if (cardCheck) {
            cardCheck.click();
            setTimeout(function () {
                if (currentTaskEl) renderPanel(currentTaskEl);
            }, 250);
        }
    });

    function editFromPanel() {
        if (!currentTaskEl) return;
        const taskId = currentTaskEl.dataset.id;
        closePanel();
        setTimeout(function () {
            const card = document.querySelector('.dash-task[data-id="' + taskId + '"]');
            if (card) openEditTaskModal(card);
        }, 150);
    }
    panelEditBtn?.addEventListener('click', editFromPanel);
    panelEditBtnFoot?.addEventListener('click', editFromPanel);

    panelDeleteBtn?.addEventListener('click', function () {
        if (!currentTaskEl) return;
        const el = currentTaskEl;
        closePanel();
        setTimeout(function () {
            el.classList.add('is-confirming');
        }, 150);
    });

    /* ============================================
       INIT
       ============================================ */
    recalculateCountsFromDOM();
    refreshStats();
    applyFilter();

})();