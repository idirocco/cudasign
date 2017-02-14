<?php 

class Documents
{
    /**
     * Hold the Cudasign cURL session
     * @var Library\Curl Curl Object
     */
    protected $curl;

    /**
     * Initialise the object load master class
     */
    public function __construct(Cudasign $master)
    {
        //associate curl class
        $this->curl = $master->curl();
    }

    /**
     * Uploads a file and creates a document. This endpoint accepts .doc, .docx, .pdf, and .png file types.
     *
     * @param  array $data document details
     * @return array returns details of the document
     */
    public function upload(array $data)
    {
        return $this->curl->post('document', $data);
    }

    /**
     * Uploads a file that contains CudaSign Document Field Tags. This endpoint only accepts .pdf, .doc or .docx files.
     *
     * @param  array $data document details
     * @return array returns details of the document
     */
    public function uploadFieldextract(array $data)
    {
        return $this->curl->post('document/fieldextract', $data);
    }

    /**
     * Update an existing document. Add fields [signature | text | initials | checkbox ], elements [signature | text | check]
     *
     * @param  string $id cudasign document id
     * @param  array $data document fields
     * @return array returns details of the document
     */
    public function update($id, $data)
    {
        return $this->curl->put('document/' . $id, $data, 'json');
    }

    /**
     * Create a new document by copying a flattened document. If a name is not supplied than it will default to the original document's name.
     *
     * @param  string $id cudasign template id
     * @param  array $data new document name
     * @return array returns details of the document
     */
    public function copy($id, $data)
    {
        return $this->curl->post("template/{$id}/copy", $data, 'json');
    }    

    /**
     * Returns a document
     *
     * @param  varchar   $id cudasign document id
     * @return array returns document details
     */
    public function getById($id)
    {
        return $this->curl->get('document/' . $id);
    }

    /**
     * Downloads a document
     *
     * @param  varchar   $id cudasign document id
     * @return returns document details
     */
    public function download($id, $type='collapsed')
    {
        $this->curl->setResponseType('raw');
        return $this->curl->get('document/' . $id . '/download?type=' . $type);
    }    

    /**
     * Deletes a previously uploaded document.
     *
     * @param  varchar   $id cudasign document id
     * @return array returns document details
     */
    public function delete($id)
    {
        return $this->curl->delete('document/' . $id);
    }

    /**
     * Create an invite to sign a document. You can create a simple free form invite or a role-based invite.
     *
     * @param  array $data document details
     * @return array returns details of the document
     */
    public function invite(array $data)
    {
        if (!isset($data['from'])) {
            throw new CudasignMissingFieldError('You must include a "from" email when inviting.');
        }

        if (!isset($data['to'])) {
            throw new CudasignMissingFieldError('You must include a "to" email when inviting.');
        }

        if (!isset($data['id'])) {
            throw new CudasignMissingFieldError('You must include the document "id" when inviting.');
        }

        return $this->curl->post('document/'.$data['id'].'/invite', $data, 'json');
    }
}
