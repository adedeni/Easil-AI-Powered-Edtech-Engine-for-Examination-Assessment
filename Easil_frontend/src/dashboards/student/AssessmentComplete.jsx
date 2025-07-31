import { CheckCircle } from "lucide-react"
import { useNavigate, useParams } from "react-router-dom"

const AssessmentComplete = () => {
  const navigate = useNavigate()
  const { id } = useParams()

  // Placeholder data - this would come from the assessment submission
  const completionData = {
    courseTitle: "Fundamentals of E-Commerce - CSC 616",
    assessmentType: "Midterm Exam",
    submissionTime: new Date().toLocaleString(),
    questionsAnswered: 10,
    totalQuestions: 10,
    timeSpent: "19 minutes 32 seconds",
    message: "Your responses have been successfully submitted. Great effort!"
  }

  return (
    <div className="min-h-screen">
      <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 space-y-2 sm:space-y-0">
          <h2 className="text-xl sm:text-2xl font-semibold text-gray-800">Assessment Complete</h2>
          <button
            onClick={() => navigate('/student/assessments')}
            className="text-xs sm:text-sm text-gray-600 hover:text-gray-800 transition-colors self-start"
          >
            ← Back to Assessments
          </button>
        </div>
        
        {/* Course Title */}
        <div className="bg-white rounded-lg p-3 sm:p-4 mb-4 sm:mb-6 shadow-sm">
          <h2 className="text-lg sm:text-xl font-semibold text-gray-800 leading-tight">{completionData.courseTitle}</h2>
        </div>

      {/* Completion Card */}
      <div className="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 lg:p-8 shadow-sm text-center">
        {/* Success Icon */}
        <div className="flex justify-center mb-4 sm:mb-6">
          <div className="w-16 h-16 sm:w-20 sm:h-20 bg-green-100 rounded-full flex items-center justify-center">
            <CheckCircle className="w-10 h-10 sm:w-12 sm:h-12 text-green-600" />
          </div>
        </div>

        {/* Success Message */}
        <h3 className="text-xl sm:text-2xl font-bold text-gray-800 mb-3 sm:mb-4">Assessment Completed!</h3>
        <p className="text-gray-600 mb-6 sm:mb-8 text-sm sm:text-base">{completionData.message}</p>

        {/* Completion Details */}
        <div className="bg-gray-50 rounded-lg p-4 sm:p-6 mb-6 sm:mb-8">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
            <div className="text-left">
              <span className="font-medium text-gray-700">Assessment Type:</span>
              <p className="text-gray-600">{completionData.assessmentType}</p>
            </div>
            <div className="text-left">
              <span className="font-medium text-gray-700">Submitted:</span>
              <p className="text-gray-600 break-words">{completionData.submissionTime}</p>
            </div>
            <div className="text-left">
              <span className="font-medium text-gray-700">Questions Answered:</span>
              <p className="text-gray-600">{completionData.questionsAnswered}/{completionData.totalQuestions}</p>
            </div>
            <div className="text-left">
              <span className="font-medium text-gray-700">Time Spent:</span>
              <p className="text-gray-600">{completionData.timeSpent}</p>
            </div>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:gap-4 justify-center">
          <button 
            onClick={() => navigate('/student')}
            className="px-4 sm:px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors text-sm sm:text-base"
          >
            Return to Dashboard
          </button>
          <button 
            onClick={() => navigate('/student/results')}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 rounded-lg font-medium transition-colors text-sm sm:text-base"
          >
            View Results & Feedback
          </button>
        </div>
      </div>
    </div>
  </div>
)
}

export default AssessmentComplete
