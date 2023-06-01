<template>
  <div>
    <table>
      <tr>
        <th>
          Industries
        </th>
        <th>
          Headcount
        </th>
        <th>
          HeadQuarter Location
        </th>
        <th>
          Annual Revenue
        </th>
      </tr>
      <tr v-for="industry in this.ChosenIndustries">
        <td>
          {{ChosenIndustries}}
        </td>
        <td>
          {{ChosenHeadCounts}}
        </td>
        <td>
          {{ChosenHQs}}
        </td>
        <td>
          Currency: {{selectedAnnualRevenueCURRENCY}}
          Min: {{selectedAnnualRevenueMin}}
          Max: {{selectedAnnualRevenueMax}}
        </td>
      </tr>
    </table>
    <select v-model="selectedIndustryID">
      <option v-for="industry in this.Industries" :value="industry.ID">{{industry.industry_name}}</option>
    </select>
    <br>
    <button @click="addIndustry">Add Industry</button>
    <br>
    <select v-model="selectedHeadcount">
      <option v-for="intervals in this.HeadcountIntervals" >{{intervals.headcount_interval}}</option>
    </select><br>
    <button @click="addHeadcount">Add HeadCount</button>
    <br>
    <select v-model="selectedHQ">
      <option v-for="hq in this.Places">{{hq.industry_name}}</option>
    </select><br>
    <button @click="addHQ">Add Region</button>
    <br>
    <select v-model="selectedAnnualRevenueCURRENCY">
      <option>USD</option>
      <option>EUR</option>
    </select>
    <select v-model="selectedAnnualRevenueMin">
      <option>0</option>
      <option>0.5</option>
      <option>1</option>
      <option>2.5</option>
      <option>5</option>
      <option>10</option>
      <option>20</option>
      <option>50</option>
      <option>100</option>
      <option>500</option>
      <option>1000</option>
    </select>
    <select v-model="selectedAnnualRevenueMax">
      <option>0.5</option>
      <option>1</option>
      <option>2.5</option>
      <option>5</option>
      <option>10</option>
      <option>20</option>
      <option>50</option>
      <option>100</option>
      <option>500</option>
      <option>1000</option>
      <option>1000+</option>
    </select><br>
    <button @click="addRevenue">Add Revenue</button>
    <br>
    <button @click="linkConstructor">Search</button>
  </div>
</template>

<script>
export default {
  name: 'search-page',
    props: {
      msg: String
  },
  mounted(){
    fetch(process.env.VUE_APP_ROOT_API+'company/filter/allindustries')
      .then(response => response.json())
      .then(data => {
      const dataaray = data
      this.Industries = dataaray
    })
    fetch(process.env.VUE_APP_ROOT_API+'company/filter/allheadcounts')
      .then(response => response.json())
      .then(data => {
      const dataaray = data
      this.HeadcountIntervals = dataaray
    })
    fetch(process.env.VUE_APP_ROOT_API+'company/filter/allheadquarters')
      .then(response => response.json())
      .then(data => {
      const dataaray = data
      this.Places = dataaray
    })
  },
  data() {
    return {
      Industries: [],
      HeadcountIntervals: [],
      Places: [],
      ChosenIndustries: [],
      ChosenHeadCounts: [],
      ChosenHQs: [],
      selectedIndustryID: null,
      selectedHeadcount: null,
      selectedHQ: null,
      selectedAnnualRevenueCURRENCY: null,
      selectedAnnualRevenueMin: null,
      selectedAnnualRevenueMax: null,
      baselink: "https://www.linkedin.com/sales/search/company?query=(filters:List(",
      indlink: "(type:INDUSTRY,values:List(",
      headlink: "",
      geolink: "",
      annuallink: ""

    };
  },
    beforeRouteEnter (to, from, next) {
      const isLoggedIn = sessionStorage.getItem('LoggedInStatus')
      if (isLoggedIn) {
        next()
      } else {
        next('/login')
      }
    },
  methods: {

    redirectToResults() {
      this.$router.push({ path: '/Result'});
    },
    addIndustry(){
      const templink = "(id:"+ this.selectedIndustryID+",selectionType:INCLUDED),"
      this.indlink += templink
      this.ChosenIndustries += this.selectedIndustryID
    },
    addHeadcount(){
      fetch(process.env.VUE_APP_ROOT_API+"company/filter/headcount",{
          headers: {
            'interval': this.selectedHeadcount
          }})
          .then(response => response.json())
          .then(response => {
          const templink = "(type:COMPANY_HEADCOUNT,values:List((id:"+response.ID+",selectionType:INCLUDED),"
          this.headlink += templink
          })
      this.ChosenHeadCounts += this.selectedHeadcount
    },
    addHQ(){
      fetch(process.env.VUE_APP_ROOT_API+"company/filter/headquarters",{
          headers: {
            'name': this.selectedHQ
          }})
        .then(response => response.json())
        .then(response => {
          const templink = "(type:REGION,values:List((id:"+response.ID+",selectionType:INCLUDED),"
          this.geolink += templink
        })
      this.ChosenHQs += this.selectedHQ
    },
    addRevenue(){
      const templink = "(type:ANNUAL_REVENUE,rangeValue:(min:"+this.selectedAnnualRevenueMin+",max:"+this.selectedAnnualRevenueMax+"),selectedSubFilter:"+this.selectedAnnualRevenueCURRENCY+")))"
      this.annuallink += templink
    },
    linkConstructor() {
      if (this.indlink != "(type:INDUSTRY,values:List(") {
        this.baselink += (this.indlink + "))" )
      }
      if (this.headlink != "(type:INDUSTRY,values:List(") {
        this.baselink += (","+this.headlink+"))")
      }
      if (this.geolink != "(type:REGION,values:List(") {
        this.baselink += (","+this.geolink +"))")
      }
      if (this.annuallink != "") {
        this.baselink += (","+ this.annuallink)
      }
      console.log(this.baselink+"))")
      sessionStorage.setItem('link',this.baselink)
      this.redirectToResults()
    }
  }

}

</script>
<style>
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th, td {
  border: 1px solid #ccc;
  padding: 8px;
}

th {
  background-color: #f2f2f2;
}

.form-group {
  margin-bottom: 10px;
}

label {
  display: block;
  margin-bottom: 5px;
}

button {
  padding: 8px 16px;
  margin: 15px 15px 15px 15px;
  background-color: crimson;
  color: #fff;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: darkblue;
}
</style>