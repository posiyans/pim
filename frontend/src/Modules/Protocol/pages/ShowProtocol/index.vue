<template>
  <div class="app-container">
    <div class="row items-center q-col-gutter-md">
      <div class="row items-center q-col-gutter-md">
        <MoveProtocolToArchiveBtn v-if="!protokol.arxiv" :protocol-id="protokol.id" @reload="getProtokolInfo" />
        <div>
          <q-btn
            v-if="roles.includes('admin') && !protokol.arxiv"
            class="filter-item"
            color="secondary"
            label="Edit"
            :to="'/protocol/edit/' + protokol.id"
          />
        </div>
      </div>
    </div>
    <div v-if="protokol.arxiv" class="text-red text-weight-bold" v-html="protokol.arxiv" />
    <div :class="protokol.arxiv ? 'text-red' : ''">
      <b>{{ protokol.title }}</b> {{ protokol.descriptions.date }}
    </div>
    <div>
      Место проведения: {{ protokol.descriptions.region }}
    </div>
    <div>
      Председатель: {{ protokol.descriptions.president }}
    </div>
    <div>
      Секретарь: {{ protokol.descriptions.secretary }}
    </div>
    <div>
      Присутствовали: <b>{{ protokol.descriptions.composition }}</b>
    </div>
    <div v-if="protokol.files && protokol.files.length > 0">
      Файлы:
      <FileBlock v-for="(f, index) in protokol.files" :file="f" :key="f.id" :index="++index" />
    </div>
    <div class="q-pt-md">
      <div v-for="partition in protokol.partition" :key="partition.id" style="padding-bottom: 20px">
        <p><b>{{ partition.number }}. {{ partition.text }}</b>
        </p>
        <p v-if="partition.speaker">
          <i>Докладчик: {{ partition.speaker }}</i>
        </p>
        <div v-if="partition.task">
          <b>ПОСТАВЛЕННЫЕ ЗАДАЧИ:</b>
          <ShowTaskList :list="partition.task" :partition-number="partition.number " />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { fetchProtokol } from 'src/Modules/Protocol/api/protocol.js'
import MoveProtocolToArchiveBtn from 'src/Modules/Protocol/components/MoveProtocolToArchiveBtn/index.vue'
// import ShowTime from 'src/components/ShowTime/index.vue'
import ShowTaskList from 'src/Modules/Task/components/ShowTaskList/index.vue'
import FileBlock from 'src/Modules/Files/components/FileBlock/index.vue'

export default {
  components: {
    FileBlock,
    MoveProtocolToArchiveBtn,
    // ShowTime,
    ShowTaskList
  },
  data() {
    return {
      data: {},
      showEditForm: false,
      protokol: {
        id: '',
        title: '',
        descriptions: {},
        files: []
      }
    }
  },
  computed: {
    user() {
      return this.$store.state.user.info
    },
    roles() {
      return this.user.roles
    }
  },
  mounted() {
    this.getProtokolInfo()
  },
  methods: {
    done(task) {
      const report = task.view_report
      let done = 0
      let not_performed = 0
      for (const item in report) {
        if (report[item].executor !== 0) {
          if (report[item].done == null) {
            not_performed++
          } else {
            done++
          }
        }
      }
      const total = done / (done + not_performed)
      return total === 1 ? 'text-teal-10' : 'text-red-10'
    },
    getProtokolInfo() {
      const data = {
        id: this.$route.params.id
      }
      fetchProtokol(data)
        .then(response => {
          this.protokol = response.data
        })
    },
    taskShow(task) {
      this.$router.push('/task/show/' + task.id)
    }
  }
}
</script>

<style scoped>
.task-text {
  padding: 1px;
  font-style: italic;
}

.link {
  color: #000061;
  padding: 0 15px;
}

.link:hover {
  color: #0000ff;
  text-decoration: underline;
  cursor: pointer;
}


</style>
