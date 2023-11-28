<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import {computed, reactive, ref} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {createUpload} from "@mux/upchunk";


defineProps({
  files: Array,
})

const file = ref(null)

const initialState = {
  file: null,
  uploader: null,
  progress: 0,
  uploading: false,
}

const state = reactive({
  ...initialState,
  formattedProgress: computed(() => Math.round(state.progress)),
  reset: () => Object.assign(state, initialState),
})

const cancel = () => {
  state.uploader.abort()
  state.reset()
}

const submit = () => {
  state.file = file.value.files[0];

  if (!state.file) {
    return
  }

  state.uploader = createUpload({
    endpoint: route('files.store'),
    method: 'post',
    file: state.file,
    chunkSize: 10 * 1024, // 10 mb
    headers: {
      'X-CSRF-TOKEN': usePage().props.csrf_token,
    },
  });

  state.uploader.on('attempt', () => {
    state.uploading = true
  })

  state.uploader.on('progress', progress => {
    state.progress = progress.detail;
  })

  state.uploader.on('success', () => {
    state.reset()
    router.reload({
      only: ['files'],
      preserveScroll: true,
    })
  })
}
</script>

<template>
  <Head title="Dashboard"/>

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form class="p-6 text-gray-900 space-y-4" @submit.prevent="submit">

            <div class="flex items-center">
              <input type="file" name="file" ref="file" class="flex-grow">
              <PrimaryButton>Upload</PrimaryButton>
            </div>

            <div v-if="state.uploading">
              <div class="bg-gray-100 shadow-inner h-3 rounded-full overflow-hidden">
                <div class="bg-blue-500 h-full transform-all duration-200" :style="{width: `${state.progress}%`}"></div>
              </div>

              <div class="flex items-center justify-between text-sm space-y-2">
                <div>
                  <button type="button" @click="cancel" class="text-blue-500">取消</button>
                </div>
                <div>{{ `${state.formattedProgress}%` }}</div>
              </div>
            </div>

          </form>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <template v-if="files.length">
              <div v-for="file in files" :key="file.id">
                {{ file.file_path }}
              </div>
            </template>
            <template v-else>
              暂时没有文件!
            </template>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
