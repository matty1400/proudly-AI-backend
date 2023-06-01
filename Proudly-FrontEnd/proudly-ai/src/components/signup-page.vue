<template>
  <div class="login">
      <div >
      <h2>Signup</h2>
      <form @submit.prevent="signup">
        <label for="username">Username:</label>
        <input type="text" id="username" v-model="username" required>

        <label for="E-Mail">E-mail:</label>
        <input type="text" id="mail" v-model="mail" required>

        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required>

        <input type="submit" value="Signup">
      </form>

    </div>
  </div>
</template>

<script>
export default {
  name: 'signup-page',
  props: {
    msg: String
  },
  data () {
    return {
      username: document.getElementById('username'),
      mail: document.getElementById('mail'),
      password: document.getElementById('password'),
      isLoggedIn: false
    }
  },
  methods: {
    signup(){
      fetch(process.env.VUE_APP_ROOT_API+'user/postUser?username='+this.username+'+&mail='+this.mail+'+&password='+this.password, {method: "POST"})
              .then(response => response.json())
              .then(response => console.log(response))

    }
  }
}
</script>
<style scoped>
h2 {
  text-align: center;
}

form {
  width: 300px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-top: 100px;
}

form label {
  display: block;
  margin-bottom: 10px;
}

form input[type="text"],
form input[type="password"] {
  width: 100%;
  padding: 5px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

form input[type="submit"] {
  width: 100%;
  background-color: crimson;
  color: #fff;
  padding: 10px;
  border: none;
  border-radius: 3px;
  cursor: pointer;
}

form input[type="submit"]:hover {
  background-color: #45a049;
}

</style>
