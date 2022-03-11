<template xmlns="http://www.w3.org/1999/html">
    <div class="container">
        <div class="form-group">
            <input type="text" @blur="saveName" v-model="name" class="form-control"
                   :class="{ 'is-invalid': $v.name.$error }">
            <div class="invalid-feedback" v-if="!$v.name.required">
                Обов'язкове поле.
            </div>
            <div class="invalid-feedback" v-if="!$v.name.maxLength">
                Макс. кількість символів: {{ $v.name.$params.maxLength.max }}
            </div>
        </div>
        <form @submit.prevent="addNewDeskList">
            <div class="form-group">
                <input type="text" v-model="desk_list_name" class="form-control"
                       :class="{ 'is-invalid': $v.desk_list_name.$error }" placeholder="Введіть назву списку">
                <div class="invalid-feedback" v-if="!$v.desk_list_name.required">
                    Обов'язкове поле.
                </div>
                <div class="invalid-feedback" v-if="!$v.desk_list_name.maxLength">
                    Макс. кількість символів: {{ $v.desk_list_name.$params.maxLength.max }}
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Додати список</button>
        </form>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Помилка завантаження даних
        </div>
        <div class="row">
            <div class="col-lg-4" v-for="(desk_list, index) in desk_lists" :key="desk_list.id">
                <div class="card mt-3">
                    <div class="card-body">
                        <form @submit.prevent="updateDeskList(desk_list.id, desk_list.name)"
                              v-if="desk_lists_input_id == desk_list.id"
                              class="d-flex justify-content-between align-items-center mb-3">
                            <input type="text" v-model="desk_list.name" class="form-control"
                                   :class="{ 'is-invalid': $v.desk_lists.$each[index].$error }"
                                   placeholder="Введіть назву списку">
                            <div class="invalid-feedback" v-if="!$v.desk_lists.$each[index].name.required">
                                Обов'язкове поле!.
                            </div>
                            <div class="invalid-feedback" v-if="!$v.desk_lists.$each[index].name.maxLength">
                                Макс. кількість символів: {{ $v.desk_lists.$each[index].name.$params.maxLength.max }}
                            </div>
                            <button type="button" class="close m-lg-1 " aria-label="Close"
                                    @click="updateDeskList(desk_list.id, desk_list.name)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </form>
                        <h4 v-else class="card-title d-flex justify-content-between align-items-center mb-3"
                            style="cursor: pointer;" @click="desk_lists_input_id = desk_list.id">{{ desk_list.name }} <i
                            class="fa-solid fa-pen-to-square"></i></h4>

                        <button type="button" class="btn btn-danger" @click="deleteDeskList(desk_list.id)">
                            Видалити
                        </button>
                    <div class="card mt-3 bg-light" v-for="card in desk_list.cards" :key="card.id" >
                        <div class="card-body">
                            <h4 class="card-title d-flex justify-content-between align-items-center" style="cursor: pointer;">{{card.name}}</h4>
                            <button type="button" @click="getCard(card.id)" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                                Відкрити
                            </button>
                            <button type="button" class="btn btn-secondary" @click="deleteCard(card.id)">Видалити</button>
                        </div>
                    </div>
                    <form @submit.prevent="addNewCard(desk_list.id)" class="mt-3">
                        <input type="text" v-model="card_names[desk_list.id]" :class="{ 'is-invalid': $v.card_names.$each[desk_list.id].$error }" class="form-control" placeholder="Введіть назву картки">
                        <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].required">
                            Обов'язкове поле!.
                        </div>
                        <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].maxLength">
                            Макс. кількість символів: {{ $v.card_names.$each[desk_list.id].$params.maxLength.max }}
                        </div>
                    </form>
                        <!-- Modal -->
                        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <form @submit.prevent="updateCardName" v-if="show_card_name_input" class="d-flex justify-content-between align-items-center">
                                            <input type="text" v-model="current_card.name" class="form-control"
                                                   :class="{ 'is-invalid': $v.current_card.name.$error }" placeholder="Введіть назву картки">
                                            <div class="invalid-feedback ml-3" v-if="!$v.current_card.name.required">
                                                Обов'язкове поле!.
                                            </div>
                                            <div class="invalid-feedback" v-if="!$v.current_card.name.maxLength">
                                                Макс. кількість символів: {{ $v.current_card.name.$params.maxLength.max }}
                                            </div>
                                            <button type="button" class="close m-lg-1" @click="updateCardName" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </form>
                                        <h5 class="modal-title" style="cursor: pointer;" id="staticBackdropLabel" v-if="!show_card_name_input" @click="show_card_name_input = true">{{current_card.name}}<i
                                            class="fa-solid fa-pen-to-square ml-3"></i></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-check" v-for="(task, index) in current_card.tasks">
                                            <input class="form-check-input" type="checkbox"
                                                  @change="updateTask(current_card.tasks[index])" :id="'inlineCheckbox' + index" v-model="current_card.tasks[index].is_done">
                                            <form @submit.prevent="updateTask(current_card.tasks[index])" v-if="task_input_name_id == task.id">
                                                <input type="text" v-model="current_card.tasks[index].name" v-if="task_input_name_id == task.id" class="form-control"

                                                       placeholder="Введіть назву задачи">
                                            </form>
<!--                                            :class="{ 'is-invalid': $v.desk_lists.$each[index].$error }"-->
                                            <label v-else class="form-check-label" :for="'inlineCheckbox' + index">{{task.name}}<i
                                                class="fa-solid fa-pen-to-square ml-3" v-if="task_input_name_id != task.id" @click="task_input_name_id = task.id" style="font-size: 13px;"></i></label>
                                            <button type="button" @click="deleteTask(task.id)" class="close ml-3" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form @submit.prevent="addNewTask" class="mt-3">
                                            <div class="form-group">
                                                <input type="text" v-model="new_task_name" class="form-control"
                                                       :class="{ 'is-invalid': $v.new_task_name.$error }"
                                                       placeholder="Введіть назву зачи">
                                                <div class="invalid-feedback" v-if="!$v.new_task_name.required">
                                                    Обов'язкове поле.
                                                </div>
                                                <div class="invalid-feedback" v-if="!$v.new_task_name.maxLength">
                                                    Макс. кількість символів: {{ $v.new_task_name.$params.maxLength.max }}
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="spinner-border" style="width: 4rem; height: 4rem;" role="status" v-if="loading">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>

<script>
import {required, maxLength} from 'vuelidate/lib/validators';

export default {
    props: [
        'deskId'
    ],
    data() {
        return {
            name: null,
            desk_list_name: null,
            errored: false,
            loading: true,
            desk_lists: [],
            desk_lists_input_id: null,
            card_names: [],
            current_card: [],
            show_card_name_input: false,
            new_task_name: '',
            task_input_name_id: null
        }
    },
    methods: {
        updateTask(task){
            axios.post('/api/V1/tasks/' + task.id, {
                _method: 'PATCH',
                name: task.name,
                is_done: task.is_done,
                card_id: task.card_id,
            })
                .then(response => {
                    this.task_input_name_id = null
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        changeTaskName(task_id){
            console.log(task_id)
            this.task_input_name_id = task_id;
        },
        deleteTask(task_id){
            axios.post('/api/V1/tasks/' + task_id, {
                _method: 'DELETE'
            })
                .then(response => {
                    this.getCard(this.current_card.id);
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        addNewTask(){
            this.$v.new_task_name.$touch();
            if (this.$v.new_task_name.$anyError) {
                return;
            }
            axios.post('/api/V1/tasks', {
                name: this.new_task_name,
                card_id: this.current_card.id
            })
                .then(response => {
                    this.$v.$reset();
                    this.new_task_name = '';
                    this.getCard(this.current_card.id);
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        updateCardName(){
            this.$v.current_card.name.$touch();
            if (this.$v.current_card.name.$anyError) {
                return;
            }
            this.show_card_name_input = false;
            axios.post('/api/V1/cards/' + this.current_card.id, {
                _method: 'PATCH',
                name: this.current_card.name,
                desk_list_id: this.current_card.desk_list_id
            })
                .then(response => {
                    this.$v.$reset();
                    this.show_card_name_input = false;
                    this.getDeskLists();
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        getCard(id){
            axios.get('/api/V1/cards/' + id)
                .then(response => {
                    this.current_card = response.data.data;
                    console.log(this.current_card)
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        deleteCard(card_id){
            if (confirm('Ви дійсно бажаєте видалити картку?')) {
                axios.post('/api/V1/cards/' + card_id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.getDeskLists();
                    })
                    .catch(error => {
                        console.error(error);
                        this.errored = true;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        },
        addNewCard(desk_list_id){
            this.$v.card_names.$each[desk_list_id].$touch();
            if (this.$v.card_names.$each[desk_list_id].$anyError) {
                return;
            }

            axios.post('/api/V1/cards', {
                name: this.card_names[desk_list_id],
                desk_list_id
            })
                .then(response => {
                    this.card_names[desk_list_id] = '';
                    this.$v.$reset();
                    this.getDeskLists();
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        getDeskLists() {
            axios.get('/api/V1/desk-lists', {
                params: {
                    desk_id: this.deskId
                }
            })
                .then(response => {
                    this.desk_lists = response.data.data;
                    this.desk_lists.forEach(el => {
                        this.card_names[el.id] = '';
                    });
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        saveName() {
            this.$v.name.$touch();
            if (this.$v.name.$anyError) {
                return;
            }
            axios.post('/api/V1/desks/' + this.deskId, {
                _method: 'PUT',
                name: this.name
            })
                .then()
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        addNewDeskList() {
            this.$v.desk_list_name.$touch();
            if (this.$v.desk_list_name.$anyError) {
                return;
            }
            console.log('huuu1')
            axios.post('/api/V1/desk-lists', {
                name: this.desk_list_name,
                desk_id: this.deskId
            })
                .then(response => {
                    this.$v.$reset();
                    this.desk_list_name = '';
                    this.desk_lists = [];
                    this.card_names = [];
                    this.getDeskLists();
                })
                .catch(error => {
                    console.error(error);
                    if (error.response.data.errors.name) {
                        this.errors = [];
                        this.errors.push(error.response.data.errors.name[0]);
                    }
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        deleteDeskList(id) {
            if (confirm('Ви дійсно бажаєте видалити список?')) {
                axios.post('/api/V1/desk-lists/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.desk_lists = [];
                        this.getDeskLists();
                    })
                    .catch(error => {
                        console.error(error);
                        this.errored = true;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        },
        updateDeskList(id, name) {
            this.$v.desk_lists.$touch();
            if (this.$v.desk_lists.$anyError) {
                return;
            }
            this.desk_lists_input_id = null;
            axios.post('/api/V1/desk-lists/' + id, {
                _method: 'PATCH',
                name,
            })
                .then(response => {
                    this.desk_lists_input_id = null;
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    },
    mounted() {
        axios.get('/api/V1/desks/' + this.deskId)
            .then(response => {
                this.name = response.data.data.name;
            })
            .catch(error => {
                console.error(error);
                this.errored = true;
            })
            .finally(() => {
                this.loading = false;
            });
        this.getDeskLists();
    },
    validations: {
        name: {
            required,
            maxLength: maxLength(255)
        },
        desk_list_name: {
            required,
            maxLength: maxLength(255)
        },
        card_names: {
            $each: {
                required,
                maxLength: maxLength(255)
            }
        },
        current_card: {
            name: {
                required,
                maxLength: maxLength(255)
            }
        },
        desk_lists: {
            $each: {
                name: {
                    required,
                    maxLength: maxLength(255)
                }
            }
        },
        new_task_name: {
            required,
            maxLength: maxLength(255)
        }
    }
}
</script>
