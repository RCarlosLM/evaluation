<template>
	<div>
		<div class="row mt-3">
			<div class="col-md-4">
        		<div class="card border-info">
					<div class="card-header bg-light">
						<h4>
							Peliculas
						</h4>
					</div>
          			<div class="card-body">
            			<div class="row">
							<div class="col-md-12">
								<label for="name" class="font-weight-bold">
									Nombre
								  	<small v-if="data_errors.name" class="form-text error-input p-1 alert-danger fade show">
								    	{{ data_errors.name[0] }}
								  	</small>
								</label>
								<div class="input-group">
									<input
										v-model="films.name"
										placeholder="Nombre"
										class="form-control"
										type="text">
									</input>
								</div> 
							</div>
							<div class="col-md-12 mt-1">
								<label for="publication_date" class="font-weight-bold">
									Fecha
									<small v-if="data_errors.publication_date" class="form-text error-input p-1 alert-danger fade show">
										{{ data_errors.publication_date[0] }}
									</small>
								</label>
								<div class="input-group">
									<input
										v-model="films.publication_date"
										class="form-control"
										type="date">
									</input>
								</div> 
							</div>
							<div class="col-md-12 mt-1">
								<label class="font-weight-bold">
									Imagen
									<small v-if="data_errors.file != undefined" class="form-text error-input p-1 alert-danger fade show">
										{{ data_errors.file[0] }}
									</small>
								</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text bg-info text-white">
											<i class="zmdi zmdi-image"></i>
										</div>
									</div>
									<input 
										id="file"
										class="border p-file form-control"
										type="file"
										ref="file"
										v-on:change="handleFileUpload()">
									</input>
								</div>
							</div>
							<div class="col-md-12 mt-1">
								<label for="status" class="font-weight-bold">
									Estatus
									<small v-if="data_errors.status != undefined" class="form-text error-input p-1 alert-danger fade show">
										{{ data_errors.status[0] }}
									</small>
								</label>
                				<div class="input-group">
									<select 
										v-model="films.status"
										placeholder="Selecciona un estatus"
										class="form-control minimal">
										<option :value="1"> Activo </option>
										<option :value="0"> Inactivo </option>
									</select>
								</div> 
							</div>

							<div class="col-md-12 mt-2">
								<button type="button" class="btn btn-primary btn-block" @click="addFilm" v-if="!isEditing">
									Agregar
								</button>
								<button class="btn btn-secondary" type="button" @click="cancelarEdit()" v-if="isEditing">
									Cancelar
								</button>
								<button class="btn btn-primary pull-right" type="button" @click="updateFilm()" v-if="isEditing">
									Actualizar
								</button>
				      		</div>

      					</div>
    				</div>
				</div>
			</div>

			<div class="col md-8">
				<div class="card border-info">
					<div class="card-header bg-light">
						<h4>
					  	Panel de peliculas
						</h4>
					</div>
					<div class="card-body">
						<div class="row mt-1 media-query-consultar">
							<div class="col-md-12">
								<vuetable ref="vuetable"
									:api-url="'/api/home/show'"
									track-by="id"
									:fields="fields"
									:css="css.tableNotResponse"
									:show-sort-icons="true"
									:per-page="5"
									pagination-path="meta"
									:append-params="moreParams"
									@vuetable:pagination-data="onPaginationData">

									<div slot="img-slot" slot-scope="props">
										<img v-if="props.rowData.cover" :src="'/storage/'+props.rowData.cover" alt="" width="80px" height="80px" class="img img-fluid zoom rounded style-img">
										<span v-else>
											Sin foto
										</span>
									</div>

									<div slot="estatus-slot" slot-scope="props">
										<i class="fa fa-check-circle fa-2x text-success" v-if="props.rowData.status">
										</i>
										<i class="fa fa-times-circle fa-2x text-warning" v-else>
										</i>
									</div>

									<div slot="opciones-slot" slot-scope="props">
										<a class="block" @click="confirmarEliminar(props.rowData)" data-toggle="tooltip" data-placement="top" title="Eliminar">
											<i class="fa fa-trash text-danger fa-1x">
											</i>
										</a>
										<a class="text-info inline-block" @click="editFilm(props.rowData)">
				              				<i class="fa fa-edit text-info fa-1x ml-2">
      										</i>
				            			</a>
										<a class="text-primary font-weight-bold block fa-1x mt-1 ml-2" 
											:href="'/films/'+props.rowData.id" data-toggle="tooltip" 
											data-placement="top" title="Turnos">
											<i class="fa fa-clock text-dark fa-1x">
											</i>
										</a>
									</div>
									
	              </vuetable>

	              <div style="margin-top:10px">
						      <div style="float: right;">
						        <vuetable-pagination ref="pagination"
											:css="css.pagination"
											class="pull-right"
											@vuetable-pagination:change-page="onChangePage">
						        </vuetable-pagination>
						      </div>
						  	</div>

							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>
</template>

<script>

	import Multiselect from 'vue-multiselect';
	import Vuetable from "vuetable-2";
	import VuetablePagination from "../utils/VuetablePaginationBootstrap4.vue";
	import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
	import CssConfig from "../utils/TableStyles";
	import VueEvents from 'vue-events'
	Vue.use(VueEvents)

	export default {
		props: [],
		components: {
			Vuetable,
			VuetablePagination,
			VuetablePaginationInfo,
			Multiselect,
		},
		data() {
			return {
				css: CssConfig,
				fields: [
					{
						name: "name",
						title: 'Nombre',
						titleClass: 'center',
						sortField: "name",
					},
					{
                        name: "img-slot",
                        title: 'Comprobante',
                        titleClass: 'center'
                    },
					{
						name: "publication_date",
						title: 'Fecha Publicación',
						titleClass: 'center',
						sortField: "publication_date",
					},
					{
						name: "estatus-slot",
						title: 'Estatus',
						titleClass: 'center',
						sortField: "status",
					},
					{
						name: "turnos",
						title: 'Turnos',
						titleClass: 'center',
						sortField: "turnos",
					},
					{
						name: "opciones-slot",
						title: 'Opciones',
						titleClass: 'center'
					}
				],
				moreParams: {},
				films: {
					name: '',
					publication_date: '',
					estatus: false,
				},
				file: '',
				data_errors: [],
				isEditing: false,

			}
		},
		created(){

		},
		methods: {

			handleFileUpload(){
                this.file = this.$refs.file.files[0];
            },

			addFilm() {

				let formData = new FormData();
                formData.append('file', this.file);
                formData.append('name', this.films.name);
                formData.append('publication_date', this.films.publication_date);
				formData.append('status', this.films.status);
                axios.post('/api/home/create',formData,{
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                }).then(res => {

					let response = res.data;
					this.$events.fire('filter-reset');
					this.$snotify.success(response.message, 'Correcto', {
						timeout: 4000,
						showProgressBar: true,
						closeOnClick: true,
						pauseOnHover: true
					});

					this.films = { 
						name: '',
						publication_date: '',
						cover: '',
						status: ''
					};

				}).catch(err => {

					let response = err.response.data;
					console.log(response);
					this.$snotify.error(response.message, 'Error', {
						timeout: 4000,
						showProgressBar: true,
						closeOnClick: true,
						pauseOnHover: true
					});

					this.data_errors = err.response.data.errors;

				})

			},

			confirmarEliminar(film){

				this.$snotify.warning('Esta seguro de eliminarlo ?', 'Aviso', {
					timeout: 0,
					showProgressBar: false,
					closeOnClick: false,
					pauseOnHover: true,
					buttons: [
						{
							text: 'Si', 
							action: (toast) => {
								this.removeFilms(film)
								this.$snotify.remove(toast.id); 
							}, 
							bold: true
						},
						{
							text: 'Cancelar', 
							action: (toast) => {
								this.$snotify.remove(toast.id); 
							}, 
							bold: true
						},
					]
				});
				
			},

			removeFilms(film){
			  axios.delete('/api/films/'+film.id).then(res => {
					this.onFilterReset();
					let response = res.data;
					let delete_row = {};
					delete_row = response.film;
					this.$events.fire('delete-rol', delete_row);
			    this.$snotify.success('Se elimino de la lista', 'Correcto', {
						timeout: 4000,
						showProgressBar: true,
						closeOnClick: true,
						pauseOnHover: true
					});
			  }).catch(error => {
			        
			  });

			},

			cancelarEdit(){
				this.isEditing = false;
				this.films = {
					name: '',
					publication_date: '',
					cover: '',
					status: false
				}
				this.file = '';
			},

			editFilm(film){
				console.log("film:", film);
				this.isEditing = true;
				this.films = {
					id: film.id,
					name: film.name,
					publication_date: film.publication_date,
					status: film.status
				};
				this.file = film.cover;
				console.log(this.films);
			},

			updateFilm(){
				let formData = new FormData();
                formData.append('file', this.file);
                formData.append('name', this.films.name);
                formData.append('publication_date', this.films.publication_date);
				formData.append('status', this.films.status);
                axios.put('/api/films/'+this.films.id,formData,{
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                }).then(res => {
					this.isEditing=false;
					this.not_direccion = false;
					let response = res.data;
					this.onFilterReset();
					this.$snotify.success(response.message, 'Correcto', {
						timeout: 4000,
						showProgressBar: true,
						closeOnClick: true,
						pauseOnHover: true
					});
					this.file = '';
					this.films = { 
						name: '',
						publication_date: '',
						cover: '',
						status: false
					};
				}).catch(err => {

					let response = err.response.data;
					this.$snotify.error(response.message, 'Error', {
						timeout: 4000,
						showProgressBar: true,
						closeOnClick: true,
						pauseOnHover: true
					});
         			 this.data_errors = err.response.data.errors;

        		});
      		},

			onFilterReset () {
				this.moreParams = {}
				Vue.nextTick( () => this.$refs.vuetable.refresh())
			},

			onPaginationData(paginationData) {
				this.$refs.pagination.setPaginationData(paginationData);
				//this.$refs.paginationInfo.setPaginationData(paginationData);
			},

			onChangePage (page) {
				this.$refs.vuetable.changePage(page)
			},

		},
		mounted() {
			this.$events.$on('filter-reset', e => this.onFilterReset());
    	},
		
	}

	
</script>
