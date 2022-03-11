<template>
    <div class="container">
        <h1>Дошки</h1>

        <form @submit.prevent="addNewDesk">
            <div class="form-group">
                <input type="text" v-model="name" class="form-control" :class="{ 'is-invalid': $v.name.$error }" placeholder="Введіть назву дошки">
                <div class="invalid-feedback" v-if="!$v.name.required">
                    Обов'язкове поле.
                </div>
                <div class="invalid-feedback" v-if="!$v.name.maxLength">
                    Макс. кількість символів: {{$v.name.$params.maxLength.max}}
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Додати</button>
        </form>
        <div class="alert alert-danger mt-3"  role="alert" v-if="errored">
            Помилка завантаження даних <br>
            {{errors[0]}}
        </div>
        <div class="row">
            <div class="col-lg-4" v-for="desk in desks">
                <div class="card mt-3">
                    <router-link class="card-body" :to="{name: 'showDesk', params: {deskId: desk.id}}">
                        <h4 class="card-title">{{desk.name}}</h4>
                    </router-link>
                    <div class="card-body">
                        <button type="button" class="btn btn-danger mt-3" @click="deleteDesk(desk.id)">Видалити</button>
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
import { required, maxLength } from 'vuelidate/lib/validators';
export default {
    data(){
        return {
            desks: [],
            errored: false,
            errors: [],
            loading: true,
            name: null
        }
    },
    mounted() {
        this.getAllDesks()
    },
    methods: {
        getAllDesks(){
            axios.get('/api/V1/desks')
                .then(response => {
                    //console.log(response);
                    this.desks = response.data.data;
                })
                .catch(error => {
                    console.error(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        deleteDesk(id){
            if(confirm('Ви дійсно бажаєте видалити дошку?')){
                axios.post('/api/V1/desks/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.desks = [];
                        this.getAllDesks();
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
        addNewDesk(){
            this.$v.$touch();
            if(this.$v.$anyError){
                return;
            }
            axios.post('/api/V1/desks', {
                name: this.name
            })
                .then(response => {
                    this.name = '';
                    this.desks = [];
                    this.$v.$reset();
                    this.getAllDesks();
                })
                .catch(error => {
                    console.error(error);
                    if (error.response.data.errors.name){
                        this.errors = [];
                        this.errors.push(error.response.data.errors.name[0]);
                    }
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    },
    validations: {
        name: {
            required,
            maxLength: maxLength(255)
        }

    }
}
</script>
