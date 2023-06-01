<template>
  <div class="Dashboard">
      <h1>Dashboard</h1>
      <button @click="redirectToSearch">SEARCH COMPANY</button>
  </div>
</template>

<script>
export default {
  name: 'Dashboard-page',
  props: {
    msg: String
  },
    beforeRouteEnter (to, from, next) {
      const isLoggedIn = sessionStorage.getItem('LoggedInStatus')
      if (isLoggedIn) {
        next()
      } else {
        next('/')
      }
    },
    methods: {
      redirectToSearch(){
        this.$router.push('/Search');
      }
    }
}
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
