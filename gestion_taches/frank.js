document.addEventListener('DOMContentLoaded', () => {
    const taskLists = document.querySelectorAll('.space-y-2');
    const addTaskForm = document.querySelector('form[action="add_task.php"]');
    const editTaskForms = document.querySelectorAll('form[action=""]');
    const profileForm = document.querySelector('form[method="POST"][enctype="multipart/form-data"]');
    const navLinks = document.querySelectorAll('nav a');
    let draggedTask = null;

    taskLists.forEach(list => {
        list.addEventListener('dragover', e => e.preventDefault());
        list.addEventListener('dragenter', () => list.classList.add('bg-gray-200'));
        list.addEventListener('dragleave', () => list.classList.remove('bg-gray-200'));
        list.addEventListener('drop', e => {
            e.preventDefault();
            list.classList.remove('bg-gray-200');
            const taskId = draggedTask.dataset.id;
            const newType = list.parentElement.querySelector('h3').textContent.includes('Simples') ? 'simple' : 
                           list.parentElement.querySelector('h3').textContent.includes('Complexes') ? 'complexe' : 'recurente';
            fetch('edit_task.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${taskId}&type=${newType}`
            }).then(() => list.appendChild(draggedTask));
        });
    });

    document.querySelectorAll('li').forEach(task => {
        task.draggable = true;
        task.dataset.id = task.querySelector('a[href*="edit_task"]').href.split('id=')[1];
        task.addEventListener('dragstart', () => draggedTask = task);
        task.addEventListener('click', e => {
            if (e.target.tagName === 'A') return;
            const statusSpan = task.querySelector('span.text-sm');
            const currentStatus = statusSpan.textContent.toLowerCase().replace(' ', '_');
            const nextStatus = currentStatus === 'en_attente' ? 'en_cours' : currentStatus === 'en_cours' ? 'terminee' : 'en_attente';
            fetch('edit_task.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${task.dataset.id}&etat=${nextStatus}`
            }).then(() => {
                statusSpan.textContent = nextStatus.replace('_', ' ');
                statusSpan.className = 'text-sm ' + (nextStatus === 'terminee' ? 'text-green-500' : nextStatus === 'en_cours' ? 'text-yellow-500' : 'text-gray-500');
                task.classList.add('animate-pulse');
                setTimeout(() => task.classList.remove('animate-pulse'), 500);
            });
        });
    });

    addTaskForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(addTaskForm);
        fetch('add_task.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
          .then(() => {
              const titre = formData.get('titre');
              const type = formData.get('type');
              const list = Array.from(taskLists).find(l => l.parentElement.querySelector('h3').textContent.toLowerCase().includes(type));
              const newTask = document.createElement('li');
              newTask.className = 'flex justify-between items-center hover:bg-gray-100 p-2 rounded';
              newTask.draggable = true;
              newTask.innerHTML = `<span>${titre}</span><div class="flex space-x-2"><span class="text-sm text-gray-500">En attente</span><a href="edit_task.php?id=latest" class="text-blue-500 hover:underline">Modifier</a><a href="delete_task.php?id=latest" class="text-red-500 hover:underline" onclick="return confirm('Supprimer cette tâche ?');">Supprimer</a></div>`;
              newTask.dataset.id = Date.now();
              list.appendChild(newTask);
              newTask.classList.add('opacity-0', 'translate-y-10');
              setTimeout(() => {
                  newTask.classList.remove('opacity-0', 'translate-y-10');
                  newTask.classList.add('transition', 'duration-300', 'opacity-100', 'translate-y-0');
              }, 10);
              addTaskForm.reset();
          });
    });

    editTaskForms.forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(form);
            fetch('edit_task.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                window.location.href = 'index.php';
                document.body.classList.add('animate-fade-out');
                setTimeout(() => document.body.classList.remove('animate-fade-out'), 500);
            });
        });
    });

    if (profileForm) {
        profileForm.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(profileForm);
            fetch('profile.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                const img = document.querySelector('img[alt="Profil"]');
                const file = formData.get('image');
                if (file.size > 0) {
                    const reader = new FileReader();
                    reader.onload = () => img.src = reader.result;
                    reader.readAsDataURL(file);
                }
                document.querySelector('input[name="nom"]').value = formData.get('nom');
                document.querySelector('input[name="email"]').value = formData.get('email');
                profileForm.parentElement.classList.add('animate-bounce');
                setTimeout(() => profileForm.parentElement.classList.remove('animate-bounce'), 1000);
            });
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            document.body.classList.add('opacity-0', 'transition', 'duration-500');
            setTimeout(() => window.location.href = link.href, 500);
        });
    });

    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Rechercher une tâche...';
    searchInput.className = 'w-full p-2 mb-4 border rounded mt-4';
    document.querySelector('main').insertBefore(searchInput, document.querySelector('.grid'));
    searchInput.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        document.querySelectorAll('li').forEach(task => {
            const text = task.querySelector('span').textContent.toLowerCase();
            task.style.display = text.includes(term) ? 'flex' : 'none';
        });
    });

    document.querySelectorAll('a[href*="delete_task.php"]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            if (confirm('Supprimer cette tâche ?')) {
                fetch(link.href, { method: 'GET' })
                    .then(() => {
                        const task = link.closest('li');
                        task.classList.add('transition', 'duration-300', 'opacity-0', 'scale-0');
                        setTimeout(() => task.remove(), 300);
                    });
            }
        });
    });

    setInterval(() => {
        fetch('index.php').then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTasks = doc.querySelectorAll('li');
                newTasks.forEach(newTask => {
                    const id = newTask.querySelector('a[href*="edit_task"]').href.split('id=')[1];
                    if (!document.querySelector(`li[data-id="${id}"]`)) {
                        const list = Array.from(taskLists).find(l => l.parentElement.querySelector('h3').textContent.toLowerCase().includes(newTask.querySelector('span').textContent.split('(')[0].trim().toLowerCase()));
                        list.appendChild(newTask.cloneNode(true));
                    }
                });
            });
    }, 30000);

    window.addEventListener('scroll', () => {
        const nav = document.querySelector('nav');
        nav.classList.toggle('shadow-lg', window.scrollY > 50);
    });

    const confetti = () => {
        for (let i = 0; i < 100; i++) {
            const div = document.createElement('div');
            div.className = 'absolute w-2 h-2 rounded-full';
            div.style.left = Math.random() * 100 + 'vw';
            div.style.top = '-10px';
            div.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
            div.style.animation = `fall ${Math.random() * 2 + 1}s linear`;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 3000);
        }
    };

    document.querySelectorAll('span.text-green-500').forEach(span => {
        if (span.textContent === 'Terminée') confetti();
    });

    document.head.insertAdjacentHTML('beforeend', `
        <style>
            @keyframes fall {
                to { transform: translateY(100vh); }
            }
            .animate-fade-out { animation: fadeOut 0.5s forwards; }
            @keyframes fadeOut { to { opacity: 0; } }
        </style>
    `);
});