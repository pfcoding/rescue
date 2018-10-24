<template>
    <div class="container">
        <div :class="{ 'error-container': hasError, 'success-container': !hasError }">
            {{ uploadStatus }}
        </div>
        <transition name="fade" mode="out-in">
            <div key="2" class="large-12 medium-12 small-12 filezone" v-if="!fileUploaded">
                <input type="file" id="files" ref="files" multiple v-on:change="handleFiles()"/>
                <p>
                    Drop json file here <br>or click to browse
                </p>

            </div>
            <div key="1" class="success-container" v-else>
                <div>
                    <label for="search">Search for Pokemon by name or type:</label>
                    <input type="text" v-model="keyword" id="search">

                    <hr>
                        <div v-for="result in results" :key="result.id">
                            {{ result.name }} ({{ result.types.join(', ') }})
                        </div>
                </div>
            </div>

        </transition>
        <div class="text-center">
            <button v-show="!fileUploaded" @click="skip" class="btn btn-primary">Skip to search</button>
        </div>
        <a class="submit-button" v-on:click="submitFiles()" v-show="!fileUploaded && files.length > 0">Submit</a>
    </div>
</template>

<script>

    import { debounce } from 'lodash/debounce';

    export default {
        props: ['input_name', 'post_url'],
        name: 'upload-files',
        data() {
            return {
                files: [],
                fileUploaded: false,
                hasError: false,
                keyword: null,
                results: {},
                uploadStatus: ''
            }
        },
        watch: {
            keyword(after, before) {
                this.fetch();
            }
        },

        methods: {
            skip: function() {
                this.fileUploaded = true;
            },
            fetch: _.debounce(function() {
                console.log(this.keyword);
                axios.get('/api/search', {params: {keyword: this.keyword}})
                    .then(response => this.results = response.data)
                    .catch(error => {
                    });
            }, 100),
            handleFiles: function() {
                let uploadedFiles = this.$refs.files.files;

                for (var i = 0; i < uploadedFiles.length; i++) {
                    this.files.push(uploadedFiles[i]);
                }
            },
            removeFile: function(key) {
                this.files.splice(key, 1);
            },
            submitFiles: function() {
                for (let i = 0; i < this.files.length; i++) {
                    if (this.files[i].id) {
                        continue;
                    }
                    let formData = new FormData();
                    formData.append('file', this.files[i]);

                    axios.post('/' + this.post_url,
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then(function (data) {
                        this.files[i].id = data['data']['id'];
                        this.files.splice(i, 1, this.files[i]);
                        this.fileUploaded = true;
                        this.hasError = false;
                        this.uploadStatus = 'Upload and import successful!';
                        console.log('success');
                    }.bind(this)).catch(data => {
                        this.hasError = true;
                        this.uploadStatus = 'Problem with upload file. Try again.';
                        console.log('error');
                    });
                }
            }
        }
    }
</script>

<style scoped>
    input[type="file"] {
        opacity: 0;
        width: 100%;
        height: 200px;
        position: absolute;
        cursor: pointer;
    }

    .filezone {
        outline: 2px dashed grey;
        outline-offset: -10px;
        background: #ccc;
        color: dimgray;
        padding: 10px 10px;
        min-height: 200px;
        position: relative;
        cursor: pointer;
    }

    .filezone:hover {
        background: #c0c0c0;
    }

    .filezone p {
        font-size: 1.2em;
        text-align: center;
        padding: 50px 50px 50px 50px;
    }


    div.success-container {
        text-align: center;
        color: green;
    }

    div.error-container {
        text-align: center;
        color: red;
    }

    a.submit-button {
        display: block;
        margin: auto;
        text-align: center;
        width: 200px;
        padding: 10px;
        text-transform: uppercase;
        background-color: #CCC;
        color: white;
        font-weight: bold;
        margin-top: 20px;
    }
</style>
