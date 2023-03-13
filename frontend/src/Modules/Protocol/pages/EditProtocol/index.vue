<template>
  <div>
    <div class="row items-center q-col-gutter-sm q-pb-xs">
      <MoveProtocolToArchiveBtn v-if="!protokol.arxiv" :protocol-id="protokol.id" @reload="getProtokolInfo" />
      <div v-if="noSave">
        <q-btn label="Отмена" color="negative" flat @click="getProtokolInfo" />
      </div>
      <div v-if="noSave">
        <q-btn label="Сохранить" color="primary" />
      </div>
    </div>
    <div v-if="protokol.arxiv" class="text-red q-pa-md" v-html="protokol.arxiv" />
    <q-card class="q-mb-sm">
      <q-card-section>

        <div class="row items-center q-col-gutter-md">
          <div class="text-teal">
            {{ protokol.title }}
            <q-popup-edit v-model="protokol.title" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
          </div>
          <div>
            от
          </div>
          <div class="text-teal">
            {{ protokol.descriptions.date }}
            <q-popup-edit v-model="protokol.descriptions.date" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
          </div>
        </div>
        <div class="row items-center">
          <div class="field-header">
            Место проведения:
          </div>
          <div class="text-teal">
            {{ protokol.descriptions.region }}
            <q-popup-edit v-model="protokol.descriptions.region" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
          </div>
        </div>
        <div class="row items-center">
          <div class="field-header">
            Председатель:
          </div>
          <div class="text-teal">
            {{ protokol.descriptions.president }}
            <q-popup-edit v-model="protokol.descriptions.president" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
          </div>
        </div>
        <div class="row items-center">
          <div class="field-header">
            Секретарь:
          </div>
          <div class="text-teal">
            {{ protokol.descriptions.secretary }}
            <q-popup-edit v-model="protokol.descriptions.secretary" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
          </div>
        </div>
        <div class="row items-center">
          <div class="field-header">
            Присутствовали:
          </div>
          <div class="text-teal">
            {{ protokol.descriptions.composition }}
            <q-popup-edit v-model="protokol.descriptions.composition" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
          </div>
        </div>
        <div class="row items-center">
          <div class="field-header">
            Тип протокола:
          </div>
          <div class="text-teal">
            <ShowTypeProtocol :type="protokol.type" />
            <q-popup-edit v-model="protokol.type" auto-save v-slot="scope">
              <QSelectTypeProtocol v-model="scope.value" />
            </q-popup-edit>
          </div>
        </div>
      </q-card-section>
    </q-card>
    <q-card class="q-mb-sm">
      <q-card-section>
        <div>
          <div class="row items-center q-col-gutter-sm">
            <div>
              Файлы:
            </div>
            <UploadFileBtn type="protocol" :id="protokol.id" @reload="getProtokolInfo" />
          </div>
          <FileBlock v-for="(f, index) in protokol.files" :file="f" :key="f.id" edit :index="++index" @reload="getProtokolInfo" />
        </div>
      </q-card-section>
    </q-card>
    <q-card>
      <q-card-section>
        <div v-for="partition in protokol.partition" :key="partition.id" style="padding-bottom: 5px">
          <div class="row items-center q-col-gutter-md q-pb-sm">
            <div class="text-teal">
              {{ partition.number }}.
              <q-popup-edit v-model="partition.number" auto-save v-slot="scope" @before-hide="changeSortTask">
                <q-input
                  v-model="scope.value"
                  dense
                  autofocus
                  type="number"
                  @keyup.enter="scope.set"

                />
              </q-popup-edit>
            </div>
            <div class="text-teal" style="flex-grow: 1;">
              {{ partition.text }}
              <q-popup-edit v-model="partition.text" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center q-col-gutter-md">
            <div>
              Докладчик:
            </div>
            <div class="text-teal">
              {{ partition.speaker }}
              <q-popup-edit v-model="partition.speaker" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <DropDownBlock :show-label="'Задачи (' + partition.task.length + ')'" :hide-label="'Задачи (' + partition.task.length + ')'">
            <ShowTaskList :list="partition.task" :partition-number="partition.number " />
          </DropDownBlock>
        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import { fetchProtokol, updateProtokol } from 'src/Modules/Protocol/api/protocol.js'
import QSelectTypeProtocol from 'src/Modules/Protocol/components/QSelectTypeProtocol/index.vue'
import MoveProtocolToArchiveBtn from 'src/Modules/Protocol/components/MoveProtocolToArchiveBtn/index.vue'
import ShowTypeProtocol from 'src/Modules/Protocol/components/ShowTypeProtocol/index.vue'
import { nextTick } from 'vue'
import DropDownBlock from 'components/DropDownBlock/index.vue'
import ShowTaskList from 'src/Modules/Task/components/ShowTaskList/index.vue'
import FileBlock from 'src/Modules/Files/components/FileBlock/index.vue'
import UploadFileBtn from 'src/Modules/Files/components/UploadFileBtn/index.vue'

export default {
  components: {
    UploadFileBtn,
    FileBlock,
    DropDownBlock,
    QSelectTypeProtocol,
    ShowTypeProtocol,
    MoveProtocolToArchiveBtn,
    ShowTaskList
  },
  data() {
    return {
      noSave: false,
      data: {},

      showFormUpload: false,
      fileUploadTo: false,
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
  watch: {
    protokol: {
      deep: true,
      handler() {
        this.noSave = true
      }
    }
  },
  mounted() {
    this.getProtokolInfo()
  },
  methods: {
    changeSortTask() {
      console.log('changesrot')
      this.protokol.partition = this.protokol.partition.sort((a, b) => {
        return a.number - b.number
      })
      let i = 1
      this.protokol.partition = this.protokol.partition.map(item => {
        item.number = i++
        return item
      })
      // this.protokol.partition.forEach(part => {
      //   part.tasks = part.tasks.sort((a, b) => {
      //     return a.number - b.number
      //   })
      //   let i = 1
      //   part.tasks = part.tasks.map(item => {
      //     item.number = i++
      //     return item
      //   })
      // })
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
      const data = {
        id: this.$route.params.id
      }
      fetchProtokol(data)
        .then(response => {
          this.protokol = response.data
          nextTick(() => {
            this.noSave = false
          })
        })
    },
    saveProtokol() {
      updateProtokol(this.protokol)
        .then(response => {
          this.$message({
            message: 'Ok',
            type: 'success',
            showClose: true,
            duration: 1000
          })
          this.getProtokolInfo()
        })
    },
    showUploadFileForm(partition) {
      console.log('upload')
      this.showFormUpload = true
      this.fileUploadTo = partition.id
      console.log(partition.id)
    },
    selectFile(event) {
      this.$message({
        message: event.target.files[0].name,
        type: 'success',
        showClose: true,
        duration: 1000
      })
      // this.fileName = event.target.files[0].name
      // this.newMessage.file = event.target.files[0]
    }
  }
}
</script>

<style scoped>
.field-header {
  min-width: 150px;
}
</style>
