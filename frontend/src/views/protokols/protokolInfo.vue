<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-download" @click="exportPorotokol()">Скачать</el-button>
      <span class="edit-button">
        <el-button v-if="roles.includes('admin') && !protokol.arxiv" class="filter-item" type="danger" @click="showFormStatus = true" >В архив </el-button>
        <el-button v-if="roles.includes('admin') && !protokol.arxiv" class="filter-item" type="danger" @click="showEditForm = true">Edit </el-button>
      </span>
    </div>
    <h4 v-if="protokol.arxiv" class="alert" v-html="protokol.arxiv"/>
    <div :class="protokol.arxiv | ArxivFilter">
      <b>{{ protokol.title }}</b> {{ protokol.descriptions.date }}
    </div>
    <div :class="protokol.arxiv | ArxivFilter">
      Место проведения: {{ protokol.descriptions.region }}
    </div>
    <div :class="protokol.arxiv | ArxivFilter">
      Председатель: {{ protokol.descriptions.president }}
    </div>
    <div :class="protokol.arxiv | ArxivFilter">
      Секретарь: {{ protokol.descriptions.secretary }}
    </div>
    <div :class="protokol.arxiv | ArxivFilter" style="padding-bottom: 20px">
      Присутствовали: <b>{{ protokol.descriptions.composition }}</b>
    </div>

    <div v-for="partition in protokol.partition" :key="partition.id" style="padding-bottom: 20px">
      <p><b>{{ partition.number }}. {{ partition.text }}</b>
        <span v-if="partition.file_name" class="link">
          <svg-icon icon-class="link" @click="getLink(partition)" />
        </span>
      </p>
      <p v-if="partition.speaker">
        <i>Докладчик: {{ partition.speaker }}</i>
      </p>
      <div v-if="partition.task">
        <b>ПОСТАВЛЕННЫЕ ЗАДАЧИ:</b>
        <div v-for="task in partition.task" :key="task.id" class="task-text">
          <span :class="done(task)" >{{ task.number }}.</span> {{ task.executor }}
          <b>{{ task.data_ispoln }}</b> {{ task.text }}
          <span class="link" @click="taskShow(task)">---></span>
        </div>
      </div>
    </div>
    <el-button class="btn btn-primary" @click="$router.go(-1)">назад</el-button>

    <el-dialog :visible.sync="showFormStatus" title="Отправить протокол и ВСЕ его задачи в Архив???" class="dialog">
      <div slot="footer" class="dialog-footer">
        <el-button @click="showFormStatus = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="danger" @click="statusArchiv">В архив</el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="showEditForm" title="Редактировать протокол" class="dialog">
      <div slot="footer" class="dialog-footer">
        <el-button @click="showFormStatus = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="danger" @click="$router.push({ name: 'ProtokolEdit', ProtokolId: $route.params.protokolId })">{{ $t('table.ok') }}</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import { fetchProtokol, downloadProtokol, protokolToArchiv } from '@/api/protokol'
import { saveAs } from 'file-saver'
import { mapGetters } from 'vuex'

export default {
  name: 'ComplexTable',
  components: { },
  filters: {
    ArxivFilter(arxiv) {
      if (arxiv) {
        return 'arxiv'
      }
      return ''
    },
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      data: {},
      showFormStatus: false,
      showEditForm: false,
      protokol: {
        title: '',
        descriptions: {

        }
      }
    }
  },
  computed: {
    ...mapGetters([
      'userId',
      'name',
      'avatar',
      'device',
      'roles'
    ])
  },
  created() {
    this.getProtokolInfo()
  },
  methods: {
    getLink(partition) {
      downloadProtokol(partition.file_hash).then(response => {
        saveAs(new Blob([response.data], {
          type: response.data.type
        }), partition.file_name)
      })
    },
    statusArchiv() {
      this.listLoading = true
      protokolToArchiv(this.$route.params.protokolId).then(response => {
        console.log(response.data)
        this.listLoading = false
        this.getProtokolInfo()
      })
      this.showFormStatus = false
    },
    done(task) {
      // console.log(task.view_report)
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
      return total === 1 ? 'success' : 'alert'
    },
    getProtokolInfo() {
      fetchProtokol(this.$route.params.protokolId).then(response => {
        this.protokol = response.data.protokol
      })
    },
    taskShow(task) {
      this.$router.push({ name: 'TaskInfo', params: { taskId: task.id }})
    },
    exportPorotokol() {
      downloadProtokol(this.$route.params.protokolId).then(response => {
        saveAs(new Blob([response.data], {
          type: response.data.type
        }), this.protokol.nomer + '.docx')
      })
    }
  }
}
</script>

<style scoped>
  .task-text {
    padding: 1px;
    font-style: italic;
  }

  .alert {
    color: red;
  }
  .arxiv {
    color: #6e0000;
  }
  .success {
    color: darkgreen;
  }
  .link{
    color: #000061;
    padding: 0 15px;
  }
  .link:hover{
    color: #0000ff;
    text-decoration: underline;
    cursor: pointer;
  }
  .edit-button{
    padding-left: 200px;
  }
  .dialog{
    background-color: #ffeaea;
    color: green;
  }
</style>
