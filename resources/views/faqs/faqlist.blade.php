
@extends('layouts.app')

@section('content')
    <div class="wrapper">

        <div class="content-page">
        <div id="faqAccordion" class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="iq-accordion career-style faq-style">
                    @foreach ($faqs as $faq)
                        <div class="card iq-accordion-block">
                            <div class="active-faq clearfix" id="headingOne">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <a role="contentinfo" class="accordion-title" data-toggle="collapse"
                                                data-target="#answer{{$faq->id}}" aria-expanded="true" aria-controls="collapseOne">
                                                @php $locale = session()->get('locale'); @endphp
                                                <span>@if($locale == 'en') {{ $faq->question_eng }} @else {{ $faq->question_mm }} @endif</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-details collapse" id="answer{{$faq->id}}" aria-labelledby="headingOne"
                                data-parent="#faqAccordion">
                                <p class="mb-0">@if($locale == 'en') {{ $faq->answer_eng }} @else {{ $faq->answer_mm }} @endif  </p>
                            </div>
                        </div>

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>



@endsection
