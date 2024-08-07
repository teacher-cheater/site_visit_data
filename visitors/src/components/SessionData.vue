<template>
  <div>
    <h1>Visits Table</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Ссылка на сайт</th>
          <th>Время нахождения</th>
          <th>Время, когда заходили</th>
          <th>USER_AGENT</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in visits" :key="item.id">
          <td>{{ item.id }}</td>
          <td>{{ item.url }}</td>
          <td>{{ item.time_on_page }}</td>
          <td>{{ formatDate(item.time_stamp) }}</td>
          <td>{{ item.user_agent }}</td>
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
      visits: [],
      selectedVisit: null
    }
  },
  methods: {
    formatDate(timestamp) {
      const date = new Date(timestamp)
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()
      const hours = String(date.getHours()).padStart(2, '0')
      const minutes = String(date.getMinutes()).padStart(2, '0')
      return `${day}.${month}.${year} ${hours}:${minutes}`
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
    }
  },
  mounted() {
    this.fetchVisits()
  }
}
</script>

<style></style>
