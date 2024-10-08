<template>
   <div class="league-container">
    <div class="league-table">
        <div v-if="showPopup" class="popup-message">
          {{ popupMessage }}
        </div>
      <h3>
        {{leagueTable.name}} Table 
        <p v-if="!leagueTable.finished">Week {{ currentWeek }}</p>
        <p v-if="leagueTable.finished">Winner: {{ leagueClubs[0].name }}</p>
    </h3>
      <table>
        <thead>
          <tr>
            <th>Teams</th>
            <th>PTS</th>
            <th>P</th>
            <th>W</th>
            <th>D</th>
            <th>L</th>
            <th>GF</th>
            <th>GA</th>
            <th>GD</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="club in leagueClubs" :key="club.id">
            <td>{{ club.name }}</td>
            <td>{{ club.points }}</td>
            <td>{{ club.pivot.played }}</td>
            <td>{{ club.pivot.won }}</td>
            <td>{{ club.pivot.drawn }}</td>
            <td>{{ club.pivot.lost }}</td>
            <td>{{ club.pivot.gf }}</td>
            <td>{{ club.pivot.ga }}</td>
            <td>{{ club.goalDifference }}</td>
          </tr>
        </tbody>
      </table>
      <button @click="play">Play Week</button>
      <button @click="playAll" :disabled="currentWeek == totalWeeks">Play All</button>
    </div>

    <input type="range" min="1" :max="totalWeeks" v-model="activeWeek" />
      
    <div class="match-results">
        <h3>{{ activeWeek }}<sup>th</sup> Week Fixture</h3>
      
        <div v-for="match in results[activeWeek]" :key="match.id" class="match">
            <span>
                {{ clubs[match.home_club_id] }} {{ match.status == 'completed' ? match.home_goals : '' }} - {{ match.status == 'completed' ? match.away_goals : '' }} {{ clubs[match.away_club_id] }}
            </span>
        </div>
      <button @click="prevWeek" :disabled="activeWeek <= 1">Prev Week</button>
      <button @click="nextWeek" :disabled="activeWeek >= totalWeeks">Next Week</button>
    </div>

    <div class="week-slider">
      <h3> Predictions of Championship</h3>
      <ul>
        <li v-for="percent, club in predictions" :key="club">
          {{ club }}: {{ percent.toFixed(2) }}%
        </li>
      </ul>
    </div>
  </div>
  </template>
  
  <script>
  import {ulid} from 'ulid';
import { computed } from 'vue';

  export default {
    props: ['sessionId'],
    data() {
        return {
            uri: 'http://localhost:8000',
            pageId: null,
            leagueTable: [],
            results: [],
            clubs: [],
            currentWeek: 1,
            activeWeek: 1,
            totalWeeks: 0,
            predictions: {},
            popupMessage: null,
            showPopup: false
        };
    },
    computed: {
        leagueClubs() {
            if (!this.leagueTable.clubs) return [];
            return this.leagueTable.clubs.map((club) => ({
                ...club,
                points: club.pivot.won * 3 + club.pivot.drawn,
                goalDifference: club.pivot.gf - club.pivot.ga,
            }));
        },
    },
    methods: {
        nextWeek() {
            if (this.activeWeek < this.totalWeeks) {
                this.activeWeek++;
            }
        },
        prevWeek() {
            if (this.activeWeek > 1) {
                this.activeWeek--;
            }
        },
        async playAll() {
            this.results = await this.get('match/play/all');
            this.currentWeek = this.totalWeeks;
            this.activeWeek = this.currentWeek;
            this.updateValues();  
        },
        async play() {
            const data = await this.get('match/play/week', { week:this.currentWeek });
            if ('message' in data) {
                this.displayPopup(data.message);
            }
            this.updateValues();
        },
        async createLeague() {
            let data = await this.post('league');
            data = data.data;
            this.leagueTable = 'data' in data ? data.data : data;
            this.currentWeek = this.leagueTable.week;
            this.activeWeek = this.currentWeek;
            const numberOfClubs = this.leagueTable.clubs.length;
            this.totalWeeks = (numberOfClubs - 1 + numberOfClubs % 2) * 2;
            Object.values(this.leagueTable.clubs).forEach(club => {
                this.clubs[club.id] = club.name;
            });     
        },
        async getLeague() {
            this.leagueTable = await this.get('league');
            this.currentWeek = this.leagueTable.week;
            this.activeWeek = this.currentWeek;
            Object.values(this.leagueTable.clubs).forEach(club => {
                this.clubs[club.id] = club.name;
            });     
        },
        async createMatches() {
            this.results = await this.post('match');   
        },
        async getMatches() {
            this.results = await this.get('match');  
        },
        async getPredictions() {
            this.predictions = await this.get('prediction');  
        },
        async get(endpoint = '', additionalParams = {}) {
            try {
                let params = {session_id: this.pageId};
                for (let key in additionalParams) {
                    params[key] = additionalParams[key];
                }
                const query = new URLSearchParams(params);
                const response = await fetch(this.uri + `/api/${endpoint}?${query}`, {
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                return await response.json();
            } catch (error) {
                console.error('Error fetching data:', error);
                return [];
            }     
        },
        async post(endpoint = '', additionalParams = {}) {
            try {
                let params = {session_id: this.pageId};
                for (let key in additionalParams) {
                    params[key] = additionalParams[key];
                }
                const response = await fetch(this.uri + `/api/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(params)
                });
                return await response.json();  
            } catch (error) {
                console.error('Error fetching data:', error);
                return [];
            } 
        },
        displayPopup(message) {
            this.popupMessage = message;
            this.showPopup = true;

            // Hide the popup after 3 seconds
            setTimeout(() => {
            this.showPopup = false;
            }, 3000);
        },
        updateValues(){
            this.getMatches();
            this.getLeague();
            this.getPredictions();
        }
    },
    created() {
        if (!this.$route.query.sessionId) {
            // Generate a new ULID
            this.pageId = ulid();

            // Update the route with the new sessionId as a query parameter
            this.$nextTick(() => {
                this.$router.replace({
                    ...this.$route,
                    query: {
                    ...this.$route.query,
                    sessionId: this.pageId,
                    },
                });
            });
        } else {
            this.pageId = this.$route.query.sessionId;
        }
    },
    mounted() {
        this.createLeague();
        this.createMatches();
        this.getPredictions();   
    },

  };
  </script>
  
  <style scoped>
    .league-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
    }
    .league-table,
    .match-results,
    .week-slider {
        flex: 1 1 300px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    button {
        margin-top: 10px;
        padding: 5px 10px;
        margin: 10px;
    }
    .match {
        margin: 10px 0;
    }
    input[type='range'] {
        width: 100%;
        margin-top: 10px;
    }
    @media (max-width: 768px) {
        .league-container {
            flex-direction: column;
            align-items: center;
        }
        .league-table,
        .match-results,
        .week-slider {
            width: 100%;
        }
    }
    .popup-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #323232;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        opacity: 0;
        animation: fadeInOut 3s forwards;
    }

        /* Animation for the popup message */
    @keyframes fadeInOut {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    10% {
        opacity: 1;
        transform: translateY(0);
    }
    90% {
        opacity: 1;
        transform: translateY(0);
    }
    100% {
        opacity: 0;
        transform: translateY(-20px);
    }
}
  </style>
