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
        <tr v-for="item in visits" :key="item.id" @click="selectedVisit(item)">
          <td>{{ item.id }}</td>
          <td>{{ item.url }}</td>
          <td>{{ getTimeOnPage(item.time_on_page) }}</td>
          <td>{{ formatDate(item.time_stamp) }}</td>
        </tr>
      </tbody>
    </table>
    <div v-if="selectedVisit">
      <h2>Visit Details</h2>
      <p><strong>IP User:</strong> {{ selectedVisit.ip_user }}</p>
      <p><strong>Scroll Percentage:</strong> {{ selectedVisit.scroll_percentage }}</p>
      <p><strong>History Click:</strong> {{ selectedVisit.history_click }}</p>
      <p><strong>User Agent:</strong> {{ selectedVisit.user_agent }}</p>
      <p><strong>Time on Page:</strong> {{ selectedVisit.time_on_page }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      visits: []
      //   selectedVisit: null
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
      return `Платформа:${platform} ОС:${os} Браузер:${browser} `
    },
    getTimeOnPage(times) {
      const minutes = Math.round(times)
      const hours = Math.floor(minutes / 60)
      const remainingMinutes = minutes % 60
      let result = ''
      if (hours > 0) {
        result += `${hours} час `
      }
      if (remainingMinutes > 0) {
        result += `${remainingMinutes} минут`
      }
      return result
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
    selectedVisit(item) {
      console.log(item)
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

h3 {
  font-size: 24px;
  margin-bottom: 20px;
}
</style>
