import { ArrowLeft, Clock, AlertTriangle, CheckCircle } from "lucide-react"
import { useNavigate } from "react-router-dom"

const AssessmentDetails = () => {
  const navigate = useNavigate()
  
  // Placeholder data for the specific assessment
  const assessment = {
    id: 1,
    title: "Fundamentals of E-Commerce - CSC 616",
    type: "Midterm Exam",
    duration: "20 min",
    attemptLimit: "1 attempt",
    instructions: [
      "The practice test consists of 20 questions covering algebraic expressions, equations, and functions.",
      "Questions are a mix of multiple-choice and short-answer formats.",
      "Review each question carefully before submitting your answers.",
      "You will receive feedback after the test including explanations for correct and incorrect answers.",
      "This test is designed to help you assess your understanding before the actual exam.",
    ],
    note: "Make sure you are in a quiet environment and have a stable internet connection before starting the test.",
  }

  return (
    <div className="min-h-screen">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-3 sm:gap-0">
          <h2 className="text-xl sm:text-2xl font-semibold text-gray-800">Assessment Details</h2>
          <button
            onClick={() => navigate('/student/assessments')}
            className="text-sm text-gray-600 hover:text-gray-800 transition-colors self-start sm:self-auto"
          >
            ← Back to Assessments
          </button>
        </div>
        
        {/* Course Title */}
        <div className="bg-white rounded-lg p-3 sm:p-4 mb-4 sm:mb-6 shadow-sm">
          <h2 className="text-lg sm:text-xl font-semibold text-gray-800">{assessment.title}</h2>
        </div>

      {/* Breadcrumb */}
      <div className="flex items-center space-x-2 text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6 overflow-x-auto">
        <span className="text-blue-600 whitespace-nowrap">Assessments</span>
        <span>›</span>
        <span className="truncate">{assessment.title}</span>
      </div>

      {/* Assessment Details Card */}
      <div className="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 shadow-sm">
        {/* Instructions Header */}
        <h3 className="text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Instructions</h3>

        {/* Assessment Info */}
        <div className="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
          <div className="flex items-center space-x-3">
            <div className="w-5 h-5 sm:w-6 sm:h-6 bg-blue-100 rounded flex items-center justify-center flex-shrink-0">
              <div className="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-blue-600 rounded"></div>
            </div>
            <div className="min-w-0 flex-1">
              <span className="font-medium text-gray-700 text-sm sm:text-base">Title: </span>
              <span className="text-gray-600 text-sm sm:text-base">{assessment.type}</span>
            </div>
          </div>

          <div className="flex items-center space-x-3">
            <Clock className="w-5 h-5 sm:w-6 sm:h-6 text-gray-400 flex-shrink-0" />
            <div className="min-w-0 flex-1">
              <span className="font-medium text-gray-700 text-sm sm:text-base">Duration: </span>
              <span className="text-gray-600 text-sm sm:text-base">{assessment.duration}</span>
            </div>
          </div>

          <div className="flex items-center space-x-3">
            <AlertTriangle className="w-5 h-5 sm:w-6 sm:h-6 text-gray-400 flex-shrink-0" />
            <div className="min-w-0 flex-1">
              <span className="font-medium text-gray-700 text-sm sm:text-base">Attempt Limit: </span>
              <span className="text-gray-600 text-sm sm:text-base">{assessment.attemptLimit}</span>
            </div>
          </div>
        </div>

        {/* Instructions List */}
        <div className="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
          {assessment.instructions.map((instruction, index) => (
            <div key={index} className="flex items-start space-x-2 sm:space-x-3">
              <CheckCircle className="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mt-0.5 flex-shrink-0" />
              <p className="text-gray-600 text-xs sm:text-sm leading-relaxed">{instruction}</p>
            </div>
          ))}
        </div>

        {/* Note Section */}
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
          <h4 className="font-semibold text-gray-800 mb-2 text-sm sm:text-base">Note</h4>
          <p className="text-gray-600 text-xs sm:text-sm leading-relaxed">{assessment.note}</p>
        </div>

        {/* Action Buttons */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-0">
          <button 
            onClick={() => navigate('/student/assessments')}
            className="flex items-center space-x-2 text-gray-600 hover:text-gray-800 transition-colors w-full sm:w-auto justify-center sm:justify-start"
          >
            <ArrowLeft className="w-4 h-4" />
            <span className="text-sm sm:text-base">Back</span>
          </button>

          <button 
            onClick={() => navigate(`/student/assessments/${assessment.id}/test`)}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition-colors flex items-center space-x-2 w-full sm:w-auto justify-center text-sm sm:text-base"
          >
            <span>Start Test</span>
            <span>→</span>
          </button>
        </div>
      </div>
    </div>
  </div>
)
}

export default AssessmentDetails
