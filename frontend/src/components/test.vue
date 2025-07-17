<template>
  <div>
    <div v-for="task in tasks" :key="task.id">
      <h3>{{ task.title }}</h3>
      <p>📌 Due: {{ formatDate(task.due_at) }}</p>
      <p>🕒 Created: {{ formatDate(task.created_at) }}</p>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'

const tasks = ref([
  {
    id: 1,
    title: 'Test task',
    created_at: '2025-07-15T10:30:00',
    due_at: '2025-07-17T16:00:00',
  },
  {
    id: 2,
    title: 'Second task',
    created_at: '2025-07-14T12:00:00',
    due_at: null,
  }
])

const formatDate = (timestamp) => {
  if (!timestamp) return 'No date';
  const date = new Date(timestamp);
  return date.toLocaleString('en-US', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  });
}
</script>
