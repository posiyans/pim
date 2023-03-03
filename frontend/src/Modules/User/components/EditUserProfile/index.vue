<template>
  <div class="q-gutter-md q-pa-md">
    <div class="row items-center">
      <div class="col-grow q-gutter-md">
        <div>
          <q-input v-model="user.name" label="Имя" outlined />
        </div>
        <div>
          <q-input v-model="user.login" label="Login" outlined />
        </div>
      </div>
      <div class="q-pa-md">
        <ChangeAvatar v-if="user.id" :id="user.id" size="120px" />
      </div>
    </div>
    <div>
      <q-input v-model="user.full_name" label="Полное имя" outlined />
    </div>
    <div>
      <q-input v-model="user.phone" label="Телефон" outlined />
    </div>
    <div>
      <QSelectExecutor v-model="user.aliases" outlined multiple label="Доступ к " />
    </div>
    <div>
      <q-checkbox
        v-model="user.login_by_sms"
        label="Вход по SMS"
        :true-value="1"
        :false-value="0"
      />
    </div>
    <div>
      <q-checkbox
        v-model="user.hide"
        label="Скрыть в списке исполнителей"
        :true-value="1"
        :false-value="0"
      />
    </div>
    <div class="text-right q-gutter-md">
      <q-btn color="negative" flat label="Отмена" @click="editCancel" />
      <q-btn color="primary" :label="user.id ? 'Сохранить' : 'Добавить'" @click="saveData" />
    </div>
  </div>
</template>

<script>
import { getUserInfo, updateUser } from 'src/Modules/User/api/user'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import ChangeAvatar from 'src/Modules/User/components/ChangeAvatar/index.vue'

export default {
  components: {
    QSelectExecutor,
    ChangeAvatar
  },
  props: {
    userId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      user: {
        login: '',
        full_name: '',
        name: '',
        phone: '',
        aliases: [],
        login_by_sms: 0,
        hide: 1
      }
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    editCancel() {
      this.$emit('cancel')
    },
    saveData() {
      updateUser(this.user)
        .then(() => {
          this.$emit('success')
        })
    },
    getData() {
      if (this.userId) {
        const data = {
          id: this.userId
        }
        getUserInfo(data)
          .then(res => {
            this.user = res.data
            this.user.aliases = this.user.aliases.map(item => +item)
          })
      }
    }

  }
}
</script>

<style scoped>

</style>
