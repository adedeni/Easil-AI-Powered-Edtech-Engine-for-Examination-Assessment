import { Calendar, Clock, FileText, CheckCircle } from "lucide-react"

const Results = () => {
  // Placeholder data for results
  const resultData = {
    courseTitle: "Fundamentals of E-Commerce - CSC 616",
    studentName: "Blessing Joy Obinna",
    studentId: "CSC/2020/054",
    assessmentTitle: "Fundamentals of E-Commerce",
    date: "August 22, 2024",
    duration: "Finished in 00:19:20 out of 00:20:00",
    overallScore: "85/100",
    grade: "A",
    strengths: [
      "Excellent understanding of e-commerce concepts, particularly in payment systems and digital marketing strategies.",
      "Your answers were well-organized and showed a clear application of theoretical knowledge to real-world scenarios.",
    ],
    improvements: [
      "Work on providing more in-depth explanations for business model-related questions, using concrete examples to support your answers.",
      "Strengthen your responses in the section on legal frameworks by expanding on regulatory considerations and their implications for online businesses.",
    ],
  }

  return (
    <div className="min-h-screen">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 className="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">Results</h2>
        
        {/* Course Title */}
        <div className="bg-gray-100 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
          <h2 className="text-lg sm:text-xl font-semibold text-gray-800 leading-tight">{resultData.courseTitle}</h2>
        </div>

        {/* Results Content */}
        <div className="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 lg:p-8">
            {/* Student Info */}
            <div className="mb-6 sm:mb-8">
              <h3 className="text-xl sm:text-2xl font-semibold text-gray-800 mb-2">{resultData.studentName}</h3>
              <p className="text-gray-600 text-sm sm:text-base">{resultData.studentId}</p>
            </div>

            <hr className="border-gray-200 mb-6 sm:mb-8" />

            {/* Summary Section */}
            <div className="mb-6 sm:mb-8">
              <h4 className="text-base sm:text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Summary</h4>

              <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div className="space-y-3 sm:space-y-4">
                  <div className="flex items-start space-x-3">
                    <FileText className="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 mt-0.5 flex-shrink-0" />
                    <div className="min-w-0">
                      <span className="font-medium text-gray-700 text-sm sm:text-base">Assessment Title: </span>
                      <span className="text-gray-600 text-sm sm:text-base break-words">{resultData.assessmentTitle}</span>
                    </div>
                  </div>

                  <div className="flex items-start space-x-3">
                    <Calendar className="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 mt-0.5 flex-shrink-0" />
                    <div className="min-w-0">
                      <span className="font-medium text-gray-700 text-sm sm:text-base">Date: </span>
                      <span className="text-gray-600 text-sm sm:text-base">{resultData.date}</span>
                    </div>
                  </div>

                  <div className="flex items-start space-x-3">
                    <Clock className="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 mt-0.5 flex-shrink-0" />
                    <div className="min-w-0">
                      <span className="font-medium text-gray-700 text-sm sm:text-base">Duration: </span>
                      <span className="text-gray-600 text-sm sm:text-base break-words">{resultData.duration}</span>
                    </div>
                  </div>
                </div>

                <div className="flex flex-col items-start lg:items-end mt-4 lg:mt-0">
                  <div className="text-left lg:text-right mb-2 sm:mb-3">
                    <p className="text-xs sm:text-sm text-gray-600 mb-1">Overall Score:</p>
                    <p className="text-2xl sm:text-3xl font-bold text-gray-800">{resultData.overallScore}</p>
                  </div>
                  <div className="text-left lg:text-right">
                    <p className="text-xs sm:text-sm text-gray-600 mb-1">Grade:</p>
                    <p className="text-xl sm:text-2xl font-bold text-gray-800">{resultData.grade}</p>
                  </div>
                </div>
              </div>
            </div>

            {/* Feedback Section */}
            <div>
              <h4 className="text-base sm:text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Feedback</h4>

              <div className="bg-gray-50 rounded-lg p-4 sm:p-6">
                {/* Strengths */}
                <div className="mb-4 sm:mb-6">
                  <h5 className="font-semibold text-gray-800 mb-3 sm:mb-4 text-sm sm:text-base">Strengths</h5>
                  <div className="space-y-2 sm:space-y-3">
                    {resultData.strengths.map((strength, index) => (
                      <div key={index} className="flex items-start space-x-2 sm:space-x-3">
                        <CheckCircle className="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mt-0.5 flex-shrink-0" />
                        <p className="text-gray-600 text-xs sm:text-sm leading-relaxed">{strength}</p>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Areas for Improvement */}
                <div>
                  <h5 className="font-semibold text-gray-800 mb-3 sm:mb-4 text-sm sm:text-base">Areas for Improvement</h5>
                  <div className="space-y-2 sm:space-y-3">
                    {resultData.improvements.map((improvement, index) => (
                      <div key={index} className="flex items-start space-x-2 sm:space-x-3">
                        <CheckCircle className="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mt-0.5 flex-shrink-0" />
                        <p className="text-gray-600 text-xs sm:text-sm leading-relaxed">{improvement}</p>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  )
}

export default Results
