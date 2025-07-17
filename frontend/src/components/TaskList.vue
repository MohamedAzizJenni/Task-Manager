<template>
  <div class="container">
    <h2>📝 Task Manager</h2>

    <input
      v-model="searchQuery"
      placeholder="🔍 Search tasks..."
      class="search-input"
    />

    <form @submit.prevent="addTask" class="task-form">
      <input v-model="newTask.title" placeholder="Task title" required />
      <textarea v-model="newTask.description" placeholder="Description"></textarea>
      <input type="datetime-local" v-model="newTask.due_at" />
      <button type="submit">➕ Add Task</button>
    </form>

    <p v-if="message" class="success-message">{{ message }}</p>

    <div class="task-grid">
<div v-for="(tasksGroup, date) in groupedTasks" :key="date">
  <h4 style="margin-bottom: 0.5rem;">📅 {{ date }}</h4>

  <div
    v-for="task in tasksGroup"
    :key="task.id"
    class="task-card"
  >        <!-- Edit mode -->
        <div v-if="editingTask === task.id">
          <input v-model="editedTask.title" :ref="setEditInput" />
          <textarea v-model="editedTask.description"></textarea>
          <div class="btn-group">
            <button @click="saveTask(task.id)" :disabled="!editedTask.title.trim()">💾 Save</button>
            <button @click="cancelEditing">❌ Cancel</button>
          </div>
        </div>
        <!-- View mode -->
        <div v-else>
          <h3 :style="{ textDecoration: task.completed ? 'line-through' : 'none' }">
            {{ task.title }}
          </h3>

<p v-if="task.due_at">📌 Due: {{ formatDate(task.due_at) }}</p>


          <div class="btn-group">
            <button @click="toggleComplete(task)">
              {{ task.completed ? '☑️ Completed' : '⬜ Mark Done' }}
            </button>
            <button @click="startEditing(task)">✏️ Edit</button>
            <button @click="deleteTask(task.id)">🗑 Delete</button>
            <button @click="toggleDescription(task.id)">
              {{ shownDescriptions.includes(task.id) ? 'Hide' : 'Show' }} Description
            </button>
          </div>

          <p v-if="shownDescriptions.includes(task.id)" class="description">
            {{ task.description }}
          </p>
          <p class="created-at">
                🕒 Created: {{ formatDate(task.created_at) }}
          </p>

        </div>
      </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, nextTick, computed} from 'vue';
import axios from 'axios';

const message = ref('');
const editInput = ref(null);
const searchQuery = ref('');
const shownDescriptions = ref([]);


const tasks = ref([]);
const newTask = ref({
  title: '',
  description: '',
  due_at: null,
});

const setEditInput = (el) => {
  editInput.value = el;
};

const editingTask = ref(null); // stores the task being edited
const editedTask = ref({
  title: '',
  description: ''
});

const API_BASE = 'http://127.0.0.1:8000/api/tasks';

const fetchTasks = async () => {
  try {
    const res = await axios.get(API_BASE)
    console.log('🔍 Tasks from API:', res.data)
    tasks.value = res.data
  } catch (err) {
    console.error('Error fetching tasks:', err)
  }
}


const addTask = async () => {
  console.log('📝 Sending task:', newTask.value) // 👈 ADD THIS
  await axios.post(API_BASE, newTask.value)
  newTask.value = { title: '', description: '', due_at: null }
  await fetchTasks()
}


const deleteTask = async (id) => {
  await axios.delete(`${API_BASE}/${id}`);
  await fetchTasks();
};

const toggleComplete = async (task) => {
  await axios.put(`${API_BASE}/${task.id}`, {
    completed: !task.completed
  });
  await fetchTasks();
};

const startEditing = (task) => {
  editingTask.value = task.id;
  editedTask.value = { title: task.title, description: task.description };

  nextTick(() => {
    if (editInput.value && typeof editInput.value.focus === 'function') {
      editInput.value.focus();
    }
  });
};

const cancelEditing = () => {
  editingTask.value = null;
  editedTask.value = { title: '', description: '' };
};

const saveTask = async (id) => {
  if (!editedTask.value.title.trim()) return;

  await axios.put(`${API_BASE}/${id}`, editedTask.value);
  editingTask.value = null;
  message.value = '✅ Task updated successfully!';
  await fetchTasks();

  // Clear the message after 3 seconds
  setTimeout(() => {
    message.value = '';
  }, 3000);
};

const filteredTasks = computed(() => {
  return tasks.value
    .filter(task =>
      task.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); // latest first
});

const groupedTasks = computed(() => {
  const groups = {};

  filteredTasks.value.forEach(task => {
    const date = new Date(task.created_at);
    const key = date.toLocaleDateString(); // group by day

    if (!groups[key]) {
      groups[key] = [];
    }

    groups[key].push(task);
  });

  return groups;
});


const toggleDescription = (id) => {
  if (shownDescriptions.value.includes(id)) {
    shownDescriptions.value = shownDescriptions.value.filter(tid => tid !== id);
  } else {
    shownDescriptions.value.push(id);
  }
};

const formatDate = (timestamp) => {
  if (!timestamp) return '—' // ← cleaner fallback
  const date = new Date(timestamp)
  return date.toLocaleString('en-US', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}


onMounted(fetchTasks);
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: auto;
  padding: 1rem;
  color: black;
  background-color: aqua;
}

h2 {
  text-align: center;
  margin-bottom: 1rem;
}

.search-input {
  width: 100%;
  padding: 8px;
  margin-bottom: 1rem;
  font-size: 1rem;
}

.task-form {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 1.5rem;
}

.task-form input,
.task-form textarea {
  padding: 8px;
  font-size: 1rem;
}

.task-form button {
  align-self: flex-start;
  padding: 8px 16px;
}

.created-at {
  font-size: 0.8rem;
  color: #888;
  margin-top: 0.5rem;
}

.due-date {
  color: #cc0000;
  font-size: 0.9rem;
  margin-top: 0.25rem;
}

.success-message {
  color: green;
  margin-bottom: 1rem;
}

.task-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.task-card {
  background-color: #fefefe;
  border: 1px solid #ddd;
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.task-card h3 {
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.btn-group {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 0.5rem;
}

.task-card button {
  padding: 6px 12px;
  font-size: 0.9rem;
  cursor: pointer;
}

.description {
  margin-top: 0.5rem;
  font-style: italic;
  color: #444;
}

</style>
