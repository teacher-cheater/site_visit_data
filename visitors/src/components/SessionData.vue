<template>
  <div>
    <h1>Visits Table</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>URL</th>
          <th>Visiting Site</th>
          <th>Time Stamp</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{}}</td>
          <td>{{}}</td>
          <td>{{}}</td>
          <td>{{}}</td>
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
    fetchVisits() {
      fetch('http://site-visit-data/collect.php')
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok')
          }
          return response.json()
        })
        .then((data) => {
          console.log('Success:', data)
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
