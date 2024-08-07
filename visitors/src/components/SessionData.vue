<template>
  <div>
    <h3>Таблица посещений</h3>
    <table class="visits-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Ссылка на сайт</th>
          <th>Время нахождения</th>
          <th>Время, когда заходили</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in visits" :key="item.id" @click="selectVisit(item)">
          <td>{{ item.id }}</td>
          <td>{{ item.url }}</td>
          <td>{{ getTimeOnPage(item.time_on_page) }}</td>
          <td>{{ formatDate(item.time_stamp) }}</td>
        </tr>
      </tbody>
    </table>
    <div v-if="selectedVisit" class="modal-dialog" @click.self="hideModalDialog">
      <div class="modal-dialog__content">
        <h2>Детали посещения</h2>
        <p><strong>IP пользователя:</strong> {{ selectedVisit.ip_user }}</p>
        <p><strong>Процент скролла:</strong> {{ selectedVisit.scroll_percentage }}</p>
        <p><strong>История кликов:</strong> {{ selectedVisit.history_click }}</p>
        <p><strong>User Agent:</strong> {{ getUserAgent(selectedVisit.user_agent) }}</p>
        <!-- <p><strong>Девайс:</strong> {{ selectedVisit.user_agent }}</p>
      <p><strong>Платформа:</strong> {{ selectedVisit.user_agent }}</p> -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      visits: [],
      selectedVisit: null
    }
  },
  methods: {
    formatDate(timestamp) {
      return new Date(timestamp).toLocaleString()
    },
    getUserAgent(data) {
      const parts = data.split(' ')
      const platform = parts[0]
      const os = parts[2]
      const browser = parts[4]
      return `Платформа: ${platform} ОС: ${os} Браузер: ${browser}`
    },
    getTimeOnPage(seconds) {
      const totalSeconds = Math.round(seconds)
      const hours = Math.floor(totalSeconds / 3600)
      const minutes = Math.floor((totalSeconds % 3600) / 60)
      const remainingSeconds = totalSeconds % 60

      let result = ''

      if (hours > 0) {
        result += `${hours} ч. `
      }
      if (minutes > 0) {
        result += `${minutes} мин. `
      }
      if (remainingSeconds > 0 || (hours === 0 && minutes === 0)) {
        result += `${remainingSeconds} сек.`
      }

      return result.trim()
    },
    fetchVisits() {
      fetch('http://site-visit-data/collect.php')
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok')
          }
          return response.json()
        })
        .then((data) => {
          this.visits = data.data
        })
        .catch((error) => {
          console.error('Error:', error)
        })
    },
    selectVisit(visit) {
      this.selectedVisit = visit
    },
    hideModalDialog() {
      this.selectedVisit = null
    }
  },

  mounted() {
    this.fetchVisits()
  }
}
</script>

<style scoped>
.visits-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 18px;
  text-align: left;
}

.visits-table th,
.visits-table td {
  padding: 12px 15px;
}

.visits-table thead th {
  background-color: #f2f2f2;
  border-bottom: 1px solid #dddddd;
}

.visits-table tbody tr {
  border-bottom: 1px solid #dddddd;
}

.visits-table tbody tr:nth-of-type(even) {
  background-color: #f9f9f9;
}

.visits-table tbody tr:hover {
  background-color: #f1f1f1;
}

.visits-table tbody tr:active {
  background-color: #e9e9e9;
}

.visits-table tbody tr:last-of-type {
  border-bottom: 2px solid #009879;
}
.visits-table tbody tr td {
  cursor: pointer;
}

h3 {
  font-size: 24px;
  margin-bottom: 20px;
}
.modal-dialog {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  position: fixed;
  display: flex;
}
.modal-dialog__content {
  margin: auto;
  background-color: #e9e9e9;
  border: 1px solid #dddddd;
  padding: 20px 30px;
}
</style>
