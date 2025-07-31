"use client"

import { ChevronLeft, ChevronRight } from "lucide-react"
import { useState } from "react"
import { useNavigate, useParams } from "react-router-dom"

const TestInterface = () => {
  const navigate = useNavigate()
  const { id } = useParams()
  const [currentAnswer, setCurrentAnswer] = useState("")
  const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0)

  // Placeholder data for the test - this would come from an API
  const testData = {
    courseTitle: "Fundamentals of E-Commerce - CSC 616",
    totalQuestions: 10,
    questions: [
      {
        id: 1,
        question: "What are the key components of an e-commerce business model?",
        instruction: "Write a brief, clear response. Ensure your answer directly addresses the question."
      },
      {
        id: 2,
        question: "Explain the difference between B2B and B2C e-commerce.",
        instruction: "Provide specific examples to support your explanation."
      },
      {
        id: 3,
        question: "What are the main security concerns in e-commerce?",
        instruction: "List and explain at least 3 major security concerns."
      },
      {
        id: 4,
        question: "Describe Facebook. Why is it so popular?",
        instruction: "Write a brief, clear response. Ensure your answer directly addresses the question."
      },
      // Add more questions as needed
    ]
  }

  const currentQuestion = testData.questions[currentQuestionIndex] || testData.questions[0]

  const handlePreviousQuestion = () => {
    if (currentQuestionIndex > 0) {
      setCurrentQuestionIndex(currentQuestionIndex - 1)
      setCurrentAnswer("") // Clear answer for previous question
    }
  }

  const handleNextQuestion = () => {
    if (currentQuestionIndex < testData.questions.length - 1) {
      setCurrentQuestionIndex(currentQuestionIndex + 1)
      setCurrentAnswer("") // Clear answer for next question
    }
  }

  const handleSubmitTest = () => {
    // Here you would typically save all answers and then navigate
    navigate(`/student/assessments/${id}/complete`)
  }

  return (
    <div className="min-h-screen">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 space-y-2 sm:space-y-0">
          <h2 className="text-xl sm:text-2xl font-semibold text-gray-800">Assessment</h2>
          <button
            onClick={() => navigate(`/student/assessments/${id}`)}
            className="text-xs sm:text-sm text-gray-600 hover:text-gray-800 transition-colors self-start"
          >
            ← Back to Assessment Details
          </button>
        </div>
      
      {/* Course Title */}
      <div className="bg-gray-100 rounded-lg p-3 sm:p-4 mb-6 sm:mb-8">
        <h2 className="text-lg sm:text-xl font-semibold text-gray-800 leading-tight">{testData.courseTitle}</h2>
      </div>

      {/* Progress Indicator */}
      <div className="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 space-y-1 sm:space-y-0">
          <span className="text-xs sm:text-sm font-medium text-gray-700">
            Question {currentQuestionIndex + 1} of {testData.totalQuestions}
          </span>
          <span className="text-xs sm:text-sm text-gray-500">
            {Math.round(((currentQuestionIndex + 1) / testData.totalQuestions) * 100)}% Complete
          </span>
        </div>
        <div className="w-full bg-gray-200 rounded-full h-1.5 sm:h-2">
          <div 
            className="bg-blue-600 h-1.5 sm:h-2 rounded-full transition-all duration-300" 
            style={{ width: `${((currentQuestionIndex + 1) / testData.totalQuestions) * 100}%` }}
          ></div>
        </div>
      </div>

      {/* Question Section */}
      <div className="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 lg:p-8">
        {/* Question */}
        <div className="mb-4 sm:mb-6">
          <h3 className="text-base sm:text-lg font-medium text-gray-800 mb-3 sm:mb-4 leading-relaxed">
            {currentQuestionIndex + 1}) {currentQuestion.question}
          </h3>

          {/* Answer Input */}
          <textarea
            value={currentAnswer}
            onChange={(e) => setCurrentAnswer(e.target.value)}
            placeholder="Your answer here"
            className="w-full h-28 sm:h-32 p-3 sm:p-4 border border-gray-300 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
          />

          {/* Instruction */}
          <p className="text-xs sm:text-sm text-gray-500 mt-2">• {currentQuestion.instruction}</p>
        </div>

        {/* Navigation Buttons */}
        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center pt-4 sm:pt-6 space-y-3 sm:space-y-0">
          <button
            onClick={handlePreviousQuestion}
            disabled={currentQuestionIndex === 0}
            className={`flex items-center justify-center space-x-2 px-4 sm:px-6 py-2 border border-gray-300 rounded-lg transition-colors text-sm sm:text-base ${
              currentQuestionIndex === 0 
                ? 'text-gray-400 cursor-not-allowed' 
                : 'text-gray-600 hover:bg-gray-50'
            }`}
          >
            <ChevronLeft className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            <span>Previous question</span>
          </button>

          {currentQuestionIndex === testData.questions.length - 1 ? (
            <button
              onClick={handleSubmitTest}
              className="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2 text-sm sm:text-base"
            >
              <span>Submit Test</span>
              <span>✓</span>
            </button>
          ) : (
            <button
              onClick={handleNextQuestion}
              className="flex items-center justify-center space-x-2 px-4 sm:px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors text-sm sm:text-base"
            >
              <span>Next question</span>
              <ChevronRight className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            </button>
          )}
        </div>
      </div>
      </div>
    </div>
  )
}

export default TestInterface
