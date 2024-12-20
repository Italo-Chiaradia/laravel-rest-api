<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\InvoiceResource;
use App\Traits\HttpResponses;

class InvoiceController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return InvoiceResource::collection(Invoice::with('user')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required|max:1',
            'paid' => 'required|numeric|between:0,1',
            'payment_date' => 'nullable',
            'value' => 'required|numeric|between:1, 9999.99'
        ]);
        
        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = Invoice::create($validator->validate());

        if ($created) {
            return $this->response('Invoice created', 200, new InvoiceResource($created->load('user')));
        }
        return $this->error('Invoice not created', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new InvoiceResource(Invoice::where('id', $id)->first());
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required|max:1|in:' . implode(',', ['B', 'C', 'P']),
            'paid' => 'required|numeric|between:0,1',
            'payment_date' => 'nullable|date-format:Y-m-d H:i:s',
            'value' => 'required|numeric|between:1, 9999.99'
        ]);
        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }
        $validated = $validator->validated();

        $updated = $invoice->update([
            "user_id" => $validated["user_id"],
            "type" => $validated["type"],
            "paid" => $validated["paid"],
            "value" => $validated["value"],
            "user_id" => $validated["user_id"],
            "payment_date" => $validated["paid"] ? $validated["payment_date"] : null
        ]);
        if ($updated) {
            return $this->response("Invoice updated", 200, new InvoiceResource($invoice->load('user')));
        }
        return $this->error("Invoice not updated", 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $deleted = $invoice->delete();
        if ($deleted) {
            return $this->response('Invoice deleted', 200);
        }
        return $this->error('Invoice deleted', 200);
    }
}
