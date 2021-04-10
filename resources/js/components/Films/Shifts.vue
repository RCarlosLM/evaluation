<template>
	<div>

		<div class="row mt-3">
			<div class="col-md-4">
				<div class="card border-info">
					<div class="card-header bg-light">
						<h4>
							Turnos
						</h4>
					</div>
          			<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<label for="shift" class="font-weight-bold">
									Turno
									<small v-if="data_errors.shift != undefined" class="form-text error-input p-1 alert-danger fade show">
										{{ data_errors.shift[0] }}
									</small>
								</label>
								<div class="input-group">
									<vue-timepicker v-model="shifts.shift"></vue-timepicker>
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
										v-model="shifts.status"
										placeholder="Selecciona un estatus"
										class="form-control minimal">
										<option :value="1"> Activo </option>
										<option :value="0"> Inactivo </option>
									</select>
								</div> 
							</div>

							<div class="col-md-12 mt-2">
								<button type="button" class="btn btn-primary btn-block" @click="addShift" v-if="!isEditing">
									Agregar
								</button>
								<button class="btn btn-secondary" type="button" @click="cancelarEdit()" v-if="isEditing">
									Cancelar
								</button>
								<button class="btn btn-primary pull-right" type="button" @click="updateShift()" v-if="isEditing">
									Actualizar
								</button>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				<div class="card border-info">
					<div class="card-header bg-light">
						<h4>
							Panel de turnos
						</h4>
					</div>
					<div class="card-body">
						<div class="row mt-1 media-query-consultar">
							<div class="col-md-12">
								<vuetable ref="vuetable"
									:api-url="'/api/shifts/show/'+film.id"
									track-by="id"
									:fields="fields"
									:css="css.tableNotResponse"
									:show-sort-icons="true"
									:per-page="10"
									pagination-path="meta"
									:append-params="moreParams"
									@vuetable:pagination-data="onPaginationData">

									<div slot="estatus-slot" slot-scope="props">
										<i class="fa fa-check-circle fa-2x text-success" v-if="props.rowData.estatus">
										</i>
										<i class="fa fa-times-circle fa-2x text-warning" v-else>
										</i>
									</div>

									<div slot="opciones-slot" slot-scope="props">
										<a class="block" @click="confirmarEliminar(props.rowData)" data-toggle="tooltip" data-placement="top" title="Eliminar">
											<i class="fa fa-trash text-danger fa-1x">
											</i>
										</a>
										<a class="text-info inline-block" @click="editShift(props.rowData)">
											<i class="fa fa-edit text-info fa-1x ml-2">
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
	import moment from "moment";
	import VuetablePagination from "../utils/VuetablePaginationBootstrap4.vue";
	import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
	import CssConfig from "../utils/TableStyles";
	import VueEvents from 'vue-events'
	import VueTimepicker from 'vue2-timepicker'
	import 'vue2-timepicker/dist/VueTimepicker.css'
	Vue.use(VueEvents)

	export default {
		props: ['film'],
		components: {
			Vuetable,
			VuetablePagination,
			VuetablePaginationInfo,
			Multiselect,
			VueTimepicker
		},
		data() {
			return {
				css: CssConfig,
				fields: [
					{
						name: "turno",
						title: 'Turno',
						titleClass: 'center',
						sortField: "turno",
					},
					{
						name: "estatus-slot",
						title: 'Estatus',
						titleClass: 'center',
						sortField: "estatus",
					},
					{
						name: "opciones-slot",
						title: 'Opciones',
						titleClass: 'center'
					}
				],
				moreParams: {},
				shifts: {
				  	shift: '',
					status: false
				},
				data_errors: [],
				isEditing: false,

			}
		},
		created(){

		},
		methods: {

			addShift() {

				axios.post('/api/shifts/create', {
					...this.shifts,
					film: this.film.id
				}).then(res => {

					let response = res.data;
					this.$events.fire('filter-reset');
					this.$snotify.success(response.message, 'Correcto', {
						timeout: 4000,
						showProgressBar: true,
						closeOnClick: true,
						pauseOnHover: true
					});

					this.shifts = { 
						shift: '',
						status: false
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

			confirmarEliminar(llamada){

				this.$snotify.warning('Esta seguro de eliminarla ?', 'Aviso', {
					timeout: 0,
					showProgressBar: false,
					closeOnClick: false,
					pauseOnHover: true,
					buttons: [
						{
							text: 'Si', 
							action: (toast) => {
								this.removeTurno(llamada)
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

			removeTurno(turno){
			  axios.delete('/api/shifts/'+turno.id).then(res => {
					this.onFilterReset();
					let response = res.data;
					let delete_row = {};
					delete_row = response.turnos;
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
				this.shifts = {
					shift: '',
					status: false
			}
      },

			editShift(turno){
				console.log("turno:", turno);
				this.isEditing = true;
				this.shifts = {
					id: turno.id,
					shift: turno.turno,
					status: turno.estatus,
				};
				console.log(this.shifts);
			},

			updateShift(){
				axios.put('/api/shifts/'+this.shifts.id, {
					...this.shifts,
					film: this.film.id
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
					this.shifts = { 
						shift: '',
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
